<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Ticket;
use App\Models\User;
use App\Mail\ChecklistCompletado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    /**
     * Mostrar formulario de checkout
     */
    public function checkoutForm(Ticket $ticket): View
    {

        if (!$ticket->canCheckout()) {
            abort(403, 'No se puede realizar checkout en este ticket.');
        }

        return view('checklists.checkout', compact('ticket'));
    }

    /**
     * Procesar checkout
     */
    public function processCheckout(Request $request, Ticket $ticket): RedirectResponse
    {
        //$this->authorize('checkout', $ticket);

        if (!$ticket->canCheckout()) {
            return back()->with('error', 'No se puede realizar checkout en este ticket.');
        }

        $validated = $this->validateChecklist($request, 'salida');

        // Generar folio numérico
        $folio = now()->format('Ymd') . str_pad($ticket->id, 6, '0', STR_PAD_LEFT);

        // Guardar imagen del canvas si existe
        $imagePath = null;
        if ($request->has('condicion_carroceria_imagen') && !empty($request->input('condicion_carroceria_imagen'))) {
            $imageData = $request->input('condicion_carroceria_imagen');
            
            // Decodificar la imagen base64
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                $imageData = base64_decode($imageData);
                
                if ($imageData !== false) {
                    $fileName = 'checklist_' . $ticket->id . '_' . time() . '.' . $type;
                    $path = 'checklists/' . $fileName;
                    
                    Storage::disk('public')->put($path, $imageData);
                    $imagePath = $path;
                }
            }
        }

        $checklist = Checklist::create([
            'ticket_id' => $ticket->id,
            'tipo_inspeccion' => 'salida',
            'folio' => $folio,
            'fecha' => $request->input('fecha', now()->toDateString()),
            'destino' => $request->input('destino'),
            'modelo' => $request->input('modelo'),
            'placas' => $request->input('placas'),
            'marca' => $request->input('marca'),
            'hora_salida' => $request->input('hora_salida'),
            'kilometraje_inicial' => $request->input('kilometraje_inicial'),
            'nivel_combustible_inicial' => $request->input('nivel_combustible_inicial'),
            'responsable_recibo_uso' => $request->input('responsable_recibo_uso'),
            'responsable_entrega' => $request->input('responsable_entrega'),
            'mantenimiento_preventivo' => $request->input('mantenimiento_preventivo'),
            'mantenimiento_correctivo' => $request->input('mantenimiento_correctivo'),
            'condicion_carroceria_log' => $request->input('condicion_carroceria_log'),
            'condicion_carroceria_imagen' => $imagePath,
            ...$validated,
        ]);

        $ticket->update([
            'status' => 'en_uso',
            'checkout_at' => now(),
        ]);

        // Actualizar kilometraje y estado del vehículo
        $ticket->vehicle->update([
            'current_mileage' => $request->input('kilometraje_inicial'),
            'status' => 'en_uso',
        ]);

        // Enviar correo a solicitante y encargados notificando el check-out
        $this->notifyChecklistCompletado($ticket, $checklist, 'salida');

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Checkout realizado exitosamente. Se han enviado notificaciones por correo.');
    }

    /**
     * Mostrar formulario de checkin
     */
    public function checkinForm(Ticket $ticket): View
    {
        $this->authorize('checkin', $ticket);

        if (!$ticket->canCheckin()) {
            abort(403, 'No se puede realizar checkin en este ticket.');
        }

        $checkoutChecklist = $ticket->checkoutChecklist;

        return view('checklists.checkin', compact('ticket', 'checkoutChecklist'));
    }

    /**
     * Procesar checkin
     */
    public function processCheckin(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('checkin', $ticket);

        if (!$ticket->canCheckin()) {
            return back()->with('error', 'No se puede realizar checkin en este ticket.');
        }

        $validated = $this->validateChecklist($request, 'entrada');

        // Usar el mismo folio del checkout
        $checkoutChecklist = $ticket->checkoutChecklist;
        $folio = $checkoutChecklist ? $checkoutChecklist->folio : (now()->format('Ymd') . str_pad($ticket->id, 6, '0', STR_PAD_LEFT));

        $checklist = Checklist::create([
            'ticket_id' => $ticket->id,
            'tipo_inspeccion' => 'entrada',
            'folio' => $folio,
            'fecha' => $request->input('fecha', now()->toDateString()),
            'destino' => $request->input('destino'),
            'modelo' => $request->input('modelo'),
            'placas' => $request->input('placas'),
            'marca' => $request->input('marca'),
            'hora_entrada' => $request->input('hora_entrada'),
            'kilometraje_inicial' => $checkoutChecklist ? $checkoutChecklist->kilometraje_inicial : 0,
            'kilometraje_final' => $request->input('kilometraje_final'),
            'nivel_combustible_inicial' => $checkoutChecklist ? $checkoutChecklist->nivel_combustible_inicial : '',
            'nivel_combustible_final' => $request->input('nivel_combustible_final'),
            'responsable_recibo_uso' => $request->input('responsable_recibo_uso'),
            'responsable_entrega' => $request->input('responsable_entrega'),
            'mantenimiento_preventivo' => $request->input('mantenimiento_preventivo'),
            'mantenimiento_correctivo' => $request->input('mantenimiento_correctivo'),
            'condicion_carroceria_log' => $request->input('condicion_carroceria_log'),
            ...$validated,
        ]);

        $ticket->update([
            'status' => 'completado',
            'checkin_at' => now(),
        ]);

        // Actualizar kilometraje y estado del vehículo
        $ticket->vehicle->update([
            'current_mileage' => $request->input('kilometraje_final'),
            'status' => 'disponible',
        ]);

        // Enviar correo a solicitante y encargados notificando el check-in
        $this->notifyChecklistCompletado($ticket, $checklist, 'entrada');

        // Notificar al usuario para que califique
        $ticket->user->notify(new \App\Notifications\CheckinCompleted($ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Checkin realizado exitosamente. Se han enviado notificaciones por correo.');
    }

    /**
     * Validar datos del checklist
     */
    private function validateChecklist(Request $request, string $tipo): array
    {
        $rules = [
            'destino' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'placas' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'fecha' => 'required|date',
            'kilometraje_inicial' => 'required|numeric|min:0',
            'nivel_combustible_inicial' => 'required|string',
            'hora_salida' => $tipo === 'salida' ? 'required' : 'nullable',
            'hora_entrada' => $tipo === 'entrada' ? 'required' : 'nullable',
            'responsable_recibo_uso' => 'required|string|max:255',
            'responsable_entrega' => 'required|string|max:255',
            // Llantas
            'llanta_delantera_derecha' => 'nullable|boolean',
            'llanta_delantera_izquierda' => 'nullable|boolean',
            'llanta_trasera_derecha' => 'nullable|boolean',
            'llanta_trasera_izquierda' => 'nullable|boolean',
            'llanta_refaccion' => 'nullable|boolean',
            'presion_adecuada' => 'nullable|boolean',
            // Frontal
            'parabrisas' => 'nullable|boolean',
            'cofre' => 'nullable|boolean',
            'parrilla' => 'nullable|boolean',
            'defensas' => 'nullable|boolean',
            'molduras' => 'nullable|boolean',
            'placa' => 'nullable|boolean',
            'salpicadera' => 'nullable|boolean',
            'antena' => 'nullable|boolean',
            // Luces
            'intermitentes' => 'nullable|boolean',
            'direccional_derecha' => 'nullable|boolean',
            'direccional_izquierda' => 'nullable|boolean',
            'luz_stop' => 'nullable|boolean',
            'faros' => 'nullable|boolean',
            'luces_altas' => 'nullable|boolean',
            'luz_interior' => 'nullable|boolean',
            'calaveras_buen_estado' => 'nullable|boolean',
            // Seguridad (Otros)
            'mata_chispas' => 'nullable|boolean',
            'alarma' => 'nullable|boolean',
            'extintor' => 'nullable|boolean',
            'botiquin' => 'nullable|boolean',
            'tarjeta_circulacion' => 'nullable|boolean',
            'licencia_conducir_vigente' => 'nullable|boolean',
            'poliza_seguro' => 'nullable|boolean',
            'triangulo_emergencia' => 'nullable|boolean',
            // Interior
            'tablero_indicadores' => 'nullable|boolean',
            'switch_encendido' => 'nullable|boolean',
            'controles_ac' => 'nullable|boolean',
            'defroster' => 'nullable|boolean',
            'radio' => 'nullable|boolean',
            'volante' => 'nullable|boolean',
            'bolsas_aire' => 'nullable|boolean',
            'cinturon_seguridad' => 'nullable|boolean',
            'coderas' => 'nullable|boolean',
            'espejo_interior' => 'nullable|boolean',
            'freno_mano' => 'nullable|boolean',
            'encendedor' => 'nullable|boolean',
            'guantera' => 'nullable|boolean',
            'manijas_interiores' => 'nullable|boolean',
            'seguros' => 'nullable|boolean',
            'asientos' => 'nullable|boolean',
            'tapetes_delanteros_traseros' => 'nullable|boolean',
            // Motor
            'nivel_aceite_motor' => 'nullable|boolean',
            'nivel_anticongelante' => 'nullable|boolean',
            'nivel_liquido_frenos' => 'nullable|boolean',
            'bateria' => 'nullable|boolean',
            'bayoneta_aceite_motor' => 'nullable|boolean',
            'tapones' => 'nullable|boolean',
            'bocina_claxon' => 'nullable|boolean',
            'radiador' => 'nullable|boolean',
            // Herramienta
            'gato' => 'nullable|boolean',
            'llave_ruedas' => 'nullable|boolean',
            'cables_pasa_corriente' => 'nullable|boolean',
            'caja_bolsa_herramientas' => 'nullable|boolean',
            'dado_birlo_seguridad' => 'nullable|boolean',
            // Calcomanías
            'calcomanias_permisos' => 'nullable|boolean',
            'calcomania_velocidad_maxima' => 'nullable|boolean',
            // Observaciones
            'mantenimiento_preventivo' => 'nullable|string',
            'mantenimiento_correctivo' => 'nullable|string',
            'condicion_carroceria_log' => 'nullable|string',
            'condicion_carroceria_imagen' => 'nullable|string',
        ];

        if ($tipo === 'entrada') {
            $rules['kilometraje_final'] = 'required|numeric|min:0';
            $rules['nivel_combustible_final'] = 'required|string';
        }

        return $request->validate($rules);
    }

    /**
     * Notificar por correo cuando se completa un checklist
     */
    private function notifyChecklistCompletado(Ticket $ticket, Checklist $checklist, string $tipo): void
    {
        // Enviar correo al solicitante
        Mail::to($ticket->user->email)->send(new ChecklistCompletado($ticket, $checklist, $tipo));

        // Enviar correo a encargados
        $encargados = User::whereHas('roles', function ($query) {
            $query->where('name', 'encargado');
        })->get();

        foreach ($encargados as $encargado) {
            Mail::to($encargado->email)->send(new ChecklistCompletado($ticket, $checklist, $tipo));
        }
    }
}
