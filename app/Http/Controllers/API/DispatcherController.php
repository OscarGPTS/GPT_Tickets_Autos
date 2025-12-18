<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\CheckoutChecklist;
use App\Models\CheckinChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DispatcherController extends Controller
{
    /**
     * Ruta de prueba para verificar que el API funciona
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => '¡API de Despachador funcionando correctamente! 🚀',
            'timestamp' => now()->toDateTimeString(),
            'version' => '2.0.0',
            'endpoints' => [
                'POST /api/dispatcher/login' => 'Login y obtener tickets',
                'GET /api/dispatcher/all-tickets' => 'Obtener todos los tickets (prueba)',
                'POST /api/dispatcher/checklist/checkout' => 'Crear/actualizar checklist de salida',
                'POST /api/dispatcher/checklist/checkin' => 'Crear/actualizar checklist de entrada',
                'GET /api/dispatcher/ticket/{id}' => 'Obtener detalle de un ticket',
                'GET /api/dispatcher/test' => 'Esta ruta de prueba',
            ]
        ], 200);
    }

    /**
     * Obtener todos los tickets (API de prueba - sin autenticación)
     * Útil para desarrollo y testing
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTickets()
    {
        // Obtener todos los tickets con status aprobado o en_curso
        $tickets = Ticket::with([
            'user:id,name,email,phone',
            'vehicle:id,brand,model,year,plates,internal_code,color,vehicle_type',
            'conductorLicense:id,license_number,license_type,expiry_date',
            'conductorLicense.user:id,name',
            'dispatcher:id,name,email',
            'checkoutChecklist',
            'checkinChecklist'
        ])
        ->whereIn('status', ['aprobado', 'en_curso'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'folio' => $ticket->folio ?? '',
                'status' => $ticket->status,
                'destination' => $ticket->destination ?? '',
                'purpose' => $ticket->purpose ?? '',
                'passenger_count' => $ticket->passenger_count ?? 1,
                'additional_notes' => $ticket->additional_notes ?? '',
                'requested_date' => $ticket->requested_date?->format('Y-m-d') ?? '',
                'requested_time_start' => $ticket->requested_time_start ?? '',
                'requested_time_end' => $ticket->requested_time_end ?? '',
                'conductor_name' => $ticket->conductor_name ?? '',
                'conductor_phone' => $ticket->conductor_phone ?? '',
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s') ?? null,
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s') ?? null,
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s') ?? null,
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s') ?? null,
                
                // Usuario solicitante
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name ?? '',
                    'email' => $ticket->user->email ?? '',
                    'phone' => $ticket->user->phone ?? '',
                ] : null,
                
                // Despachador asignado
                'dispatcher' => $ticket->dispatcher ? [
                    'id' => $ticket->dispatcher->id,
                    'name' => $ticket->dispatcher->name ?? '',
                    'email' => $ticket->dispatcher->email ?? '',
                ] : null,
                
                // Vehículo asignado
                'vehicle' => $ticket->vehicle ? [
                    'id' => $ticket->vehicle->id,
                    'brand' => $ticket->vehicle->brand ?? '',
                    'model' => $ticket->vehicle->model ?? '',
                    'year' => $ticket->vehicle->year ?? 0,
                    'plates' => $ticket->vehicle->plates ?? '',
                    'internal_code' => $ticket->vehicle->internal_code ?? '',
                    'color' => $ticket->vehicle->color ?? '',
                    'vehicle_type' => $ticket->vehicle->vehicle_type ?? '',
                ] : null,
                
                // Licencia del conductor
                'conductor_license' => $ticket->conductorLicense ? [
                    'id' => $ticket->conductorLicense->id,
                    'license_number' => $ticket->conductorLicense->license_number ?? '',
                    'license_type' => $ticket->conductorLicense->license_type ?? '',
                    'expiry_date' => $ticket->conductorLicense->expiry_date?->format('Y-m-d') ?? '',
                    'full_name' => $ticket->conductorLicense->user?->name ?? $ticket->conductor_name ?? '',
                ] : null,
                
                // Checklists (vacíos si no existen)
                'checkout_checklist' => $this->formatChecklistForMobile($ticket, 'salida'),
                'checkin_checklist' => $this->formatChecklistForMobile($ticket, 'entrada'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Tickets obtenidos correctamente (modo prueba)',
            'total' => $tickets->count(),
            'data' => [
                'tickets' => $tickets
            ]
        ], 200);
    }

    /**
     * Login o verificación de usuario por OAuth2
     * Busca por correo y nombre, retorna tickets disponibles con sus checklists
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por correo y nombre
        $user = User::where('email', $request->email)
                    ->where('name', $request->name)
                    ->where('is_active', true)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado o inactivo'
            ], 404);
        }

        // TODO: Descomentar validación de rol despachador en producción
        // Verificar que el usuario tenga el rol de despachador
        // $isDispatcher = $user->roles()->where('name', 'despachador')->exists();

        // if (!$isDispatcher) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'El usuario no tiene permisos de despachador'
        //     ], 403);
        // }

        // Obtener todos los tickets asignados al despachador
       
        $tickets = Ticket::with([
            'user:id,name,email,phone',
            'vehicle:id,brand,model,year,plates,internal_code,color,vehicle_type',
            'conductorLicense:id,license_number,license_type,expiry_date',
            'conductorLicense.user:id,name',
            'checkoutChecklist',
            'checkinChecklist'
        ])
        ->where('dispatcher_id', $user->id)
        ->whereIn('status', ['aprobado', 'en_curso'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'folio' => $ticket->folio ?? '',
                'status' => $ticket->status,
                'destination' => $ticket->destination ?? '',
                'purpose' => $ticket->purpose ?? '',
                'passenger_count' => $ticket->passenger_count ?? 1,
                'additional_notes' => $ticket->additional_notes ?? '',
                'requested_date' => $ticket->requested_date?->format('Y-m-d') ?? '',
                'requested_time_start' => $ticket->requested_time_start ?? '',
                'requested_time_end' => $ticket->requested_time_end ?? '',
                'conductor_name' => $ticket->conductor_name ?? '',
                'conductor_phone' => $ticket->conductor_phone ?? '',
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s') ?? null,
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s') ?? null,
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s') ?? null,
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s') ?? null,
                
                // Usuario solicitante
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name ?? '',
                    'email' => $ticket->user->email ?? '',
                    'phone' => $ticket->user->phone ?? '',
                ] : null,
                
                // Vehículo asignado
                'vehicle' => $ticket->vehicle ? [
                    'id' => $ticket->vehicle->id,
                    'brand' => $ticket->vehicle->brand ?? '',
                    'model' => $ticket->vehicle->model ?? '',
                    'year' => $ticket->vehicle->year ?? 0,
                    'plates' => $ticket->vehicle->plates ?? '',
                    'internal_code' => $ticket->vehicle->internal_code ?? '',
                    'color' => $ticket->vehicle->color ?? '',
                    'vehicle_type' => $ticket->vehicle->vehicle_type ?? '',
                ] : null,
                
                // Licencia del conductor
                'conductor_license' => $ticket->conductorLicense ? [
                    'id' => $ticket->conductorLicense->id,
                    'license_number' => $ticket->conductorLicense->license_number ?? '',
                    'license_type' => $ticket->conductorLicense->license_type ?? '',
                    'expiry_date' => $ticket->conductorLicense->expiry_date?->format('Y-m-d') ?? '',
                    'full_name' => $ticket->conductorLicense->user?->name ?? $ticket->conductor_name ?? '',
                ] : null,
                
                // Checklists (vacíos si no existen)
                'checkout_checklist' => $this->formatChecklistForMobile($ticket, 'salida'),
                'checkin_checklist' => $this->formatChecklistForMobile($ticket, 'entrada'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Usuario autenticado correctamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'department' => $user->department,
                    'avatar' => $user->avatar,
                ],
                'tickets' => $tickets
            ]
        ], 200);
    }

    /**
     * Crear o actualizar checklist de salida (checkout)
     * Asignación directa de campos del request al modelo
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutChecklist(Request $request)
    {
        // Validación mínima requerida
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El ticket_id es requerido',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Corregir typo común de Flutter
        if ($request->has('cintulon_seguridad')) {
            $request->merge(['cinturon_seguridad' => $request->cintulon_seguridad]);
        }
        
        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // Verificar que el ticket esté en estado aprobado
            if (!in_array($ticket->status, ['aprobado', 'en_curso'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El ticket debe estar aprobado para crear un checklist'
                ], 400);
            }

            // Buscar si ya existe un checklist de salida o crear uno nuevo
            $checklist = CheckoutChecklist::where('ticket_id', $request->ticket_id)->first();

            $isNew = false;
            if (!$checklist) {
                $checklist = new CheckoutChecklist();
                $isNew = true;
            }

            // Asignación directa de campos con valores por defecto
            $checklist->ticket_id = $request->ticket_id;
            $checklist->fecha = $request->fecha ?? now()->toDateString();
            $checklist->hora_salida = $request->hora_salida ?? null;
            $checklist->hora_entrada = $request->hora_entrada ?? null;
            $checklist->kilometraje_inicial = $request->kilometraje_inicial ?? 0;
            $checklist->kilometraje_final = $request->kilometraje_final ?? null;
            $checklist->nivel_combustible_inicial = $request->nivel_combustible_inicial ?? '1/2';
            $checklist->nivel_combustible_final = $request->nivel_combustible_final ?? null;
            
            // Llantas
            $checklist->llanta_delantera_derecha = $request->llanta_delantera_derecha;
            $checklist->llanta_delantera_izquierda = $request->llanta_delantera_izquierda;
            $checklist->llanta_delantera_vida = $request->llanta_delantera_vida;
            $checklist->llanta_trasera_derecha = $request->llanta_trasera_derecha;
            $checklist->llanta_trasera_izquierda = $request->llanta_trasera_izquierda;
            $checklist->llanta_trasera_vida = $request->llanta_trasera_vida;
            $checklist->llanta_refaccion = $request->llanta_refaccion;
            $checklist->presion_adecuada = $request->presion_adecuada;
            
            // Frontal
            $checklist->parabrisas = $request->parabrisas;
            $checklist->cofre = $request->cofre;
            $checklist->parrilla = $request->parrilla;
            $checklist->defensas = $request->defensas;
            $checklist->molduras = $request->molduras;
            $checklist->placa = $request->placa;
            $checklist->salpicadera = $request->salpicadera;
            $checklist->antena = $request->antena;
            
            // Luces
            $checklist->intermitentes = $request->intermitentes;
            $checklist->direccional_derecha = $request->direccional_derecha;
            $checklist->direccional_izquierda = $request->direccional_izquierda;
            $checklist->luz_stop = $request->luz_stop;
            $checklist->faros = $request->faros;
            $checklist->luces_altas = $request->luces_altas;
            $checklist->luz_interior = $request->luz_interior;
            $checklist->calaveras_buen_estado = $request->calaveras_buen_estado;
            
            // Seguridad
            $checklist->mata_chispas = $request->mata_chispas;
            $checklist->alarma = $request->alarma;
            $checklist->extintor = $request->extintor;
            $checklist->botiquin = $request->botiquin;
            $checklist->tarjeta_circulacion = $request->tarjeta_circulacion;
            $checklist->licencia_conducir_vigente = $request->licencia_conducir_vigente;
            $checklist->poliza_seguro = $request->poliza_seguro;
            $checklist->triangulo_emergencia = $request->triangulo_emergencia;
            
            // Interior
            $checklist->tablero_indicadores = $request->tablero_indicadores;
            $checklist->switch_encendido = $request->switch_encendido;
            $checklist->controles_ac = $request->controles_ac;
            $checklist->defroster = $request->defroster;
            $checklist->radio = $request->radio;
            $checklist->volante = $request->volante;
            $checklist->bolsas_aire = $request->bolsas_aire;
            $checklist->cinturon_seguridad = $request->cinturon_seguridad;
            $checklist->coderas = $request->coderas;
            $checklist->espejo_interior = $request->espejo_interior;
            $checklist->freno_mano = $request->freno_mano;
            $checklist->encendedor = $request->encendedor;
            $checklist->guantera = $request->guantera;
            $checklist->manijas_interiores = $request->manijas_interiores;
            $checklist->seguros = $request->seguros;
            $checklist->asientos = $request->asientos;
            $checklist->tapetes_delanteros_traseros = $request->tapetes_delanteros_traseros;
            
            // Motor
            $checklist->nivel_aceite_motor = $request->nivel_aceite_motor;
            $checklist->nivel_anticongelante = $request->nivel_anticongelante;
            $checklist->nivel_liquido_frenos = $request->nivel_liquido_frenos;
            $checklist->bateria = $request->bateria;
            $checklist->bayoneta_aceite_motor = $request->bayoneta_aceite_motor;
            $checklist->tapones = $request->tapones;
            $checklist->bocina_claxon = $request->bocina_claxon;
            $checklist->radiador = $request->radiador;
            
            // Herramienta
            $checklist->gato = $request->gato;
            $checklist->llave_ruedas = $request->llave_ruedas;
            $checklist->cables_pasa_corriente = $request->cables_pasa_corriente;
            $checklist->caja_bolsa_herramientas = $request->caja_bolsa_herramientas;
            $checklist->dado_birlo_seguridad = $request->dado_birlo_seguridad;
            
            // Calcomanías
            $checklist->calcomanias_permisos = $request->calcomanias_permisos;
            $checklist->calcomania_velocidad_maxima = $request->calcomania_velocidad_maxima;
            
            // Observaciones
            $checklist->mantenimiento_preventivo = $request->mantenimiento_preventivo;
            $checklist->mantenimiento_correctivo = $request->mantenimiento_correctivo;
            $checklist->condicion_carroceria_log = $request->condicion_carroceria_log;
            $checklist->responsable_recibo_uso = $request->responsable_recibo_uso;
            $checklist->responsable_entrega = $request->responsable_entrega;

            // Procesar imagen base64 si viene
            $imagePath = null;
            if ($request->has('condicion_carroceria_imagen') && !empty($request->condicion_carroceria_imagen)) {
                $imageData = $request->condicion_carroceria_imagen;
                
                // Decodificar la imagen base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    
                    $imageData = base64_decode($imageData);
                    
                    if ($imageData !== false) {
                        $fileName = 'checkout_' . $ticket->id . '_' . time() . '.' . $type;
                        $path = 'checklists/' . $fileName;
                        
                        \Storage::disk('public')->put($path, $imageData);
                        $imagePath = $path;
                    }
                }
            }
            
            $checklist->condicion_carroceria_imagen = $imagePath;

            $checklist->save();

            // Si es nuevo, actualizar el ticket
            if ($isNew) {
                $ticket->checkout_at = now();
                $ticket->status = 'en_curso';
                $ticket->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Checklist de salida creado correctamente' : 'Checklist de salida actualizado correctamente',
                'data' => [
                    'checklist' => $checklist,
                    'ticket' => $ticket->fresh(['vehicle', 'user'])
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el checklist de salida',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear o actualizar checklist de entrada (checkin)
     * Asignación directa de campos del request al modelo
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkinChecklist(Request $request)
    {
        // Validación mínima requerida
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El ticket_id es requerido',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Corregir typo común de Flutter
        if ($request->has('cintulon_seguridad')) {
            $request->merge(['cinturon_seguridad' => $request->cintulon_seguridad]);
        }

        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // Verificar que el ticket esté en curso
            if ($ticket->status !== 'en_curso') {
                return response()->json([
                    'success' => false,
                    'message' => 'El ticket debe estar en curso para crear un checklist de entrada'
                ], 400);
            }

            // Verificar que exista un checklist de salida
            $checkoutExists = CheckoutChecklist::where('ticket_id', $request->ticket_id)->exists();

            if (!$checkoutExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe existir un checklist de salida antes de crear uno de entrada'
                ], 400);
            }

            // Buscar si ya existe un checklist de entrada o crear uno nuevo
            $checklist = CheckinChecklist::where('ticket_id', $request->ticket_id)->first();

            $isNew = false;
            if (!$checklist) {
                $checklist = new CheckinChecklist();
                $isNew = true;
            }

            // Asignación directa de campos con valores por defecto
            $checklist->ticket_id = $request->ticket_id;
            $checklist->fecha = $request->fecha ?? now()->toDateString();
            $checklist->hora_salida = $request->hora_salida ?? null;
            $checklist->hora_entrada = $request->hora_entrada ?? null;
            $checklist->kilometraje_inicial = $request->kilometraje_inicial ?? 0;
            $checklist->kilometraje_final = $request->kilometraje_final ?? null;
            $checklist->nivel_combustible_inicial = $request->nivel_combustible_inicial ?? '1/2';
            $checklist->nivel_combustible_final = $request->nivel_combustible_final ?? null;
            
            // Llantas
            $checklist->llanta_delantera_derecha = $request->llanta_delantera_derecha;
            $checklist->llanta_delantera_izquierda = $request->llanta_delantera_izquierda;
            $checklist->llanta_delantera_vida = $request->llanta_delantera_vida;
            $checklist->llanta_trasera_derecha = $request->llanta_trasera_derecha;
            $checklist->llanta_trasera_izquierda = $request->llanta_trasera_izquierda;
            $checklist->llanta_trasera_vida = $request->llanta_trasera_vida;
            $checklist->llanta_refaccion = $request->llanta_refaccion;
            $checklist->presion_adecuada = $request->presion_adecuada;
            
            // Frontal
            $checklist->parabrisas = $request->parabrisas;
            $checklist->cofre = $request->cofre;
            $checklist->parrilla = $request->parrilla;
            $checklist->defensas = $request->defensas;
            $checklist->molduras = $request->molduras;
            $checklist->placa = $request->placa;
            $checklist->salpicadera = $request->salpicadera;
            $checklist->antena = $request->antena;
            
            // Luces
            $checklist->intermitentes = $request->intermitentes;
            $checklist->direccional_derecha = $request->direccional_derecha;
            $checklist->direccional_izquierda = $request->direccional_izquierda;
            $checklist->luz_stop = $request->luz_stop;
            $checklist->faros = $request->faros;
            $checklist->luces_altas = $request->luces_altas;
            $checklist->luz_interior = $request->luz_interior;
            $checklist->calaveras_buen_estado = $request->calaveras_buen_estado;
            
            // Seguridad
            $checklist->mata_chispas = $request->mata_chispas;
            $checklist->alarma = $request->alarma;
            $checklist->extintor = $request->extintor;
            $checklist->botiquin = $request->botiquin;
            $checklist->tarjeta_circulacion = $request->tarjeta_circulacion;
            $checklist->licencia_conducir_vigente = $request->licencia_conducir_vigente;
            $checklist->poliza_seguro = $request->poliza_seguro;
            $checklist->triangulo_emergencia = $request->triangulo_emergencia;
            
            // Interior
            $checklist->tablero_indicadores = $request->tablero_indicadores;
            $checklist->switch_encendido = $request->switch_encendido;
            $checklist->controles_ac = $request->controles_ac;
            $checklist->defroster = $request->defroster;
            $checklist->radio = $request->radio;
            $checklist->volante = $request->volante;
            $checklist->bolsas_aire = $request->bolsas_aire;
            $checklist->cinturon_seguridad = $request->cinturon_seguridad;
            $checklist->coderas = $request->coderas;
            $checklist->espejo_interior = $request->espejo_interior;
            $checklist->freno_mano = $request->freno_mano;
            $checklist->encendedor = $request->encendedor;
            $checklist->guantera = $request->guantera;
            $checklist->manijas_interiores = $request->manijas_interiores;
            $checklist->seguros = $request->seguros;
            $checklist->asientos = $request->asientos;
            $checklist->tapetes_delanteros_traseros = $request->tapetes_delanteros_traseros;
            
            // Motor
            $checklist->nivel_aceite_motor = $request->nivel_aceite_motor;
            $checklist->nivel_anticongelante = $request->nivel_anticongelante;
            $checklist->nivel_liquido_frenos = $request->nivel_liquido_frenos;
            $checklist->bateria = $request->bateria;
            $checklist->bayoneta_aceite_motor = $request->bayoneta_aceite_motor;
            $checklist->tapones = $request->tapones;
            $checklist->bocina_claxon = $request->bocina_claxon;
            $checklist->radiador = $request->radiador;
            
            // Herramienta
            $checklist->gato = $request->gato;
            $checklist->llave_ruedas = $request->llave_ruedas;
            $checklist->cables_pasa_corriente = $request->cables_pasa_corriente;
            $checklist->caja_bolsa_herramientas = $request->caja_bolsa_herramientas;
            $checklist->dado_birlo_seguridad = $request->dado_birlo_seguridad;
            
            // Calcomanías
            $checklist->calcomanias_permisos = $request->calcomanias_permisos;
            $checklist->calcomania_velocidad_maxima = $request->calcomania_velocidad_maxima;
            
            // Observaciones
            $checklist->mantenimiento_preventivo = $request->mantenimiento_preventivo;
            $checklist->mantenimiento_correctivo = $request->mantenimiento_correctivo;
            $checklist->condicion_carroceria_log = $request->condicion_carroceria_log;
            $checklist->responsable_recibo_uso = $request->responsable_recibo_uso;
            $checklist->responsable_entrega = $request->responsable_entrega;

            // Procesar imagen base64 si viene
            $imagePath = null;
            if ($request->has('condicion_carroceria_imagen') && !empty($request->condicion_carroceria_imagen)) {
                $imageData = $request->condicion_carroceria_imagen;
                
                // Decodificar la imagen base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    
                    $imageData = base64_decode($imageData);
                    
                    if ($imageData !== false) {
                        $fileName = 'checkin_' . $ticket->id . '_' . time() . '.' . $type;
                        $path = 'checklists/' . $fileName;
                        
                        \Storage::disk('public')->put($path, $imageData);
                        $imagePath = $path;
                    }
                }
            }
            
            $checklist->condicion_carroceria_imagen = $imagePath;

            $checklist->save();

            // Si es nuevo, actualizar el ticket
            if ($isNew) {
                $ticket->checkin_at = now();
                $ticket->status = 'finalizado';
                $ticket->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Checklist de entrada creado correctamente' : 'Checklist de entrada actualizado correctamente',
                'data' => [
                    'checklist' => $checklist,
                    'ticket' => $ticket->fresh(['vehicle', 'user'])
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el checklist de entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle de un ticket específico
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicket($id)
    {
        $ticket = Ticket::with([
            'user:id,name,email,phone',
            'vehicle:id,brand,model,year,plates,internal_code,color,vehicle_type',
            'conductorLicense:id,license_number,license_type,expiry_date',
            'conductorLicense.user:id,name',
            'checkoutChecklist',
            'checkinChecklist'
        ])
        ->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $ticket->id,
                'folio' => $ticket->folio ?? '',
                'status' => $ticket->status,
                'destination' => $ticket->destination ?? '',
                'purpose' => $ticket->purpose ?? '',
                'passenger_count' => $ticket->passenger_count ?? 1,
                'additional_notes' => $ticket->additional_notes ?? '',
                'requested_date' => $ticket->requested_date?->format('Y-m-d') ?? '',
                'requested_time_start' => $ticket->requested_time_start ?? '',
                'requested_time_end' => $ticket->requested_time_end ?? '',
                'conductor_name' => $ticket->conductor_name ?? '',
                'conductor_phone' => $ticket->conductor_phone ?? '',
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s') ?? null,
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s') ?? null,
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s') ?? null,
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s') ?? null,
                
                'user' => $ticket->user,
                'vehicle' => $ticket->vehicle,
                'conductor_license' => $ticket->conductorLicense,
                'checkout_checklist' => $this->formatChecklistForMobile($ticket, 'salida'),
                'checkin_checklist' => $this->formatChecklistForMobile($ticket, 'entrada'),
            ]
        ], 200);
    }

    /**
     * Formatear checklist para la app móvil
     * Retorna estructura vacía si no existe
     * 
     * @param Ticket $ticket
     * @param string $tipo
     * @return array
     */
    private function formatChecklistForMobile(Ticket $ticket, string $tipo): array
    {
        $checklist = $tipo === 'salida' 
            ? $ticket->checkoutChecklist 
            : $ticket->checkinChecklist;

        if (!$checklist) {
            // Retornar estructura vacía con valores por defecto
            return [
                'id' => null,
                'exists' => false,
                'tipo_inspeccion' => $tipo,
                'folio' => $ticket->folio ?? '',
                'fecha' => '',
                'destino' => $ticket->destination ?? '',
                'modelo' => $ticket->vehicle?->model ?? '',
                'placas' => $ticket->vehicle?->plates ?? '',
                'marca' => $ticket->vehicle?->brand ?? '',
                'hora_salida' => '',
                'hora_entrada' => '',
                'kilometraje_inicial' => 0,
                'kilometraje_final' => 0,
                'nivel_combustible_inicial' => '',
                'nivel_combustible_final' => '',
                // Secciones
                'llantas' => $this->getEmptyLlantasSection(),
                'frontal' => $this->getEmptyFrontalSection(),
                'luces' => $this->getEmptyLucesSection(),
                'seguridad' => $this->getEmptySeguridadSection(),
                'interior' => $this->getEmptyInteriorSection(),
                'motor' => $this->getEmptyMotorSection(),
                'herramienta' => $this->getEmptyHerramientaSection(),
                'calcomanias' => $this->getEmptyCalcomaniasSection(),
                'observaciones' => $this->getEmptyObservacionesSection(),
            ];
        }

        // Formatear checklist existente
        return [
            'id' => $checklist->id,
            'exists' => true,
            'tipo_inspeccion' => $tipo,
            'folio' => $ticket->folio ?? '',
            'fecha' => $checklist->fecha?->format('Y-m-d'),
            'destino' => $ticket->destination ?? '',
            'modelo' => $ticket->vehicle?->model ?? '',
            'placas' => $ticket->vehicle?->plates ?? '',
            'marca' => $ticket->vehicle?->brand ?? '',
            'hora_salida' => $checklist->hora_salida,
            'hora_entrada' => $checklist->hora_entrada,
            'kilometraje_inicial' => $checklist->kilometraje_inicial,
            'kilometraje_final' => $checklist->kilometraje_final,
            'nivel_combustible_inicial' => $checklist->nivel_combustible_inicial,
            'nivel_combustible_final' => $checklist->nivel_combustible_final,
            
            // Secciones organizadas
            'llantas' => [
                'llanta_delantera_derecha' => $checklist->llanta_delantera_derecha,
                'llanta_delantera_izquierda' => $checklist->llanta_delantera_izquierda,
                'llanta_delantera_vida' => $checklist->llanta_delantera_vida,
                'llanta_trasera_derecha' => $checklist->llanta_trasera_derecha,
                'llanta_trasera_izquierda' => $checklist->llanta_trasera_izquierda,
                'llanta_trasera_vida' => $checklist->llanta_trasera_vida,
                'llanta_refaccion' => $checklist->llanta_refaccion,
                'presion_adecuada' => $checklist->presion_adecuada,
            ],
            'frontal' => [
                'parabrisas' => $checklist->parabrisas,
                'cofre' => $checklist->cofre,
                'parrilla' => $checklist->parrilla,
                'defensas' => $checklist->defensas,
                'molduras' => $checklist->molduras,
                'placa' => $checklist->placa,
                'salpicadera' => $checklist->salpicadera,
                'antena' => $checklist->antena,
            ],
            'luces' => [
                'intermitentes' => $checklist->intermitentes,
                'direccional_derecha' => $checklist->direccional_derecha,
                'direccional_izquierda' => $checklist->direccional_izquierda,
                'luz_stop' => $checklist->luz_stop,
                'faros' => $checklist->faros,
                'luces_altas' => $checklist->luces_altas,
                'luz_interior' => $checklist->luz_interior,
                'calaveras_buen_estado' => $checklist->calaveras_buen_estado,
            ],
            'seguridad' => [
                'mata_chispas' => $checklist->mata_chispas,
                'alarma' => $checklist->alarma,
                'extintor' => $checklist->extintor,
                'botiquin' => $checklist->botiquin,
                'tarjeta_circulacion' => $checklist->tarjeta_circulacion,
                'licencia_conducir_vigente' => $checklist->licencia_conducir_vigente,
                'poliza_seguro' => $checklist->poliza_seguro,
                'triangulo_emergencia' => $checklist->triangulo_emergencia,
            ],
            'interior' => [
                'tablero_indicadores' => $checklist->tablero_indicadores,
                'switch_encendido' => $checklist->switch_encendido,
                'controles_ac' => $checklist->controles_ac,
                'defroster' => $checklist->defroster,
                'radio' => $checklist->radio,
                'volante' => $checklist->volante,
                'bolsas_aire' => $checklist->bolsas_aire,
                'cinturon_seguridad' => $checklist->cinturon_seguridad,
                'coderas' => $checklist->coderas,
                'espejo_interior' => $checklist->espejo_interior,
                'freno_mano' => $checklist->freno_mano,
                'encendedor' => $checklist->encendedor,
                'guantera' => $checklist->guantera,
                'manijas_interiores' => $checklist->manijas_interiores,
                'seguros' => $checklist->seguros,
                'asientos' => $checklist->asientos,
                'tapetes_delanteros_traseros' => $checklist->tapetes_delanteros_traseros,
            ],
            'motor' => [
                'nivel_aceite_motor' => $checklist->nivel_aceite_motor,
                'nivel_anticongelante' => $checklist->nivel_anticongelante,
                'nivel_liquido_frenos' => $checklist->nivel_liquido_frenos,
                'bateria' => $checklist->bateria,
                'bayoneta_aceite_motor' => $checklist->bayoneta_aceite_motor,
                'tapones' => $checklist->tapones,
                'bocina_claxon' => $checklist->bocina_claxon,
                'radiador' => $checklist->radiador,
            ],
            'herramienta' => [
                'gato' => $checklist->gato,
                'llave_ruedas' => $checklist->llave_ruedas,
                'cables_pasa_corriente' => $checklist->cables_pasa_corriente,
                'caja_bolsa_herramientas' => $checklist->caja_bolsa_herramientas,
                'dado_birlo_seguridad' => $checklist->dado_birlo_seguridad,
            ],
            'calcomanias' => [
                'calcomanias_permisos' => $checklist->calcomanias_permisos,
                'calcomania_velocidad_maxima' => $checklist->calcomania_velocidad_maxima,
            ],
            'observaciones' => [
                'mantenimiento_preventivo' => $checklist->mantenimiento_preventivo,
                'mantenimiento_correctivo' => $checklist->mantenimiento_correctivo,
                'condicion_carroceria_log' => $checklist->condicion_carroceria_log,
                'condicion_carroceria_imagen' => $checklist->condicion_carroceria_imagen,
                'responsable_recibo_uso' => $checklist->responsable_recibo_uso,
                'responsable_entrega' => $checklist->responsable_entrega,
            ],
        ];
    }

    // Métodos helper para secciones vacías
    private function getEmptyLlantasSection(): array
    {
        return [
            'llanta_delantera_derecha' => false,
            'llanta_delantera_izquierda' => false,
            'llanta_delantera_vida' => false,
            'llanta_trasera_derecha' => false,
            'llanta_trasera_izquierda' => false,
            'llanta_trasera_vida' => false,
            'llanta_refaccion' => false,
            'presion_adecuada' => false,
        ];
    }

    private function getEmptyFrontalSection(): array
    {
        return [
            'parabrisas' => false,
            'cofre' => false,
            'parrilla' => false,
            'defensas' => false,
            'molduras' => false,
            'placa' => false,
            'salpicadera' => false,
            'antena' => false,
        ];
    }

    private function getEmptyLucesSection(): array
    {
        return [
            'intermitentes' => false,
            'direccional_derecha' => false,
            'direccional_izquierda' => false,
            'luz_stop' => false,
            'faros' => false,
            'luces_altas' => false,
            'luz_interior' => false,
            'calaveras_buen_estado' => false,
        ];
    }

    private function getEmptySeguridadSection(): array
    {
        return [
            'mata_chispas' => false,
            'alarma' => false,
            'extintor' => false,
            'botiquin' => false,
            'tarjeta_circulacion' => false,
            'licencia_conducir_vigente' => false,
            'poliza_seguro' => false,
            'triangulo_emergencia' => false,
        ];
    }

    private function getEmptyInteriorSection(): array
    {
        return [
            'tablero_indicadores' => false,
            'switch_encendido' => false,
            'controles_ac' => false,
            'defroster' => false,
            'radio' => false,
            'volante' => false,
            'bolsas_aire' => false,
            'cinturon_seguridad' => false,
            'coderas' => false,
            'espejo_interior' => false,
            'freno_mano' => false,
            'encendedor' => false,
            'guantera' => false,
            'manijas_interiores' => false,
            'seguros' => false,
            'asientos' => false,
            'tapetes_delanteros_traseros' => false,
        ];
    }

    private function getEmptyMotorSection(): array
    {
        return [
            'nivel_aceite_motor' => false,
            'nivel_anticongelante' => false,
            'nivel_liquido_frenos' => false,
            'bateria' => false,
            'bayoneta_aceite_motor' => false,
            'tapones' => false,
            'bocina_claxon' => false,
            'radiador' => false,
        ];
    }

    private function getEmptyHerramientaSection(): array
    {
        return [
            'gato' => false,
            'llave_ruedas' => false,
            'cables_pasa_corriente' => false,
            'caja_bolsa_herramientas' => false,
            'dado_birlo_seguridad' => false,
        ];
    }

    private function getEmptyCalcomaniasSection(): array
    {
        return [
            'calcomanias_permisos' => false,
            'calcomania_velocidad_maxima' => false,
        ];
    }

    private function getEmptyObservacionesSection(): array
    {
        return [
            'mantenimiento_preventivo' => '',
            'mantenimiento_correctivo' => '',
            'condicion_carroceria_log' => [],
            'condicion_carroceria_imagen' => '',
            'responsable_recibo_uso' => '',
            'responsable_entrega' => '',
        ];
    }

    /**
     * Obtener tickets del usuario autenticado (paginados)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTickets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuario por correo
        $user = User::where('email', $request->email)
                    ->where('is_active', true)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado o inactivo'
            ], 404);
        }

        // Obtener tickets del usuario con paginación
        $tickets = Ticket::with([
            'user:id,name,email,phone',
            'vehicle:id,brand,model,year,plates,internal_code,color,vehicle_type,current_mileage',
            'dispatcher:id,name,email,phone',
            'approver:id,name',
            'conductorLicense:id,license_number,license_type,expiry_date',
            'conductorLicense.user:id,name',
            'checkoutChecklist',
            'checkinChecklist'
        ])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Formatear los tickets
        $formattedTickets = $tickets->getCollection()->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'folio' => $ticket->folio ?? '',
                'requisicion' => $ticket->requisicion ?? '',
                'status' => $ticket->status,
                'destination' => $ticket->destination ?? '',
                'cliente' => $ticket->cliente ?? '',
                'purpose' => $ticket->purpose ?? '',
                'passenger_count' => $ticket->passenger_count ?? 1,
                'additional_notes' => $ticket->additional_notes ?? '',
                'requested_date' => $ticket->requested_date?->format('Y-m-d') ?? '',
                'requested_time_start' => $ticket->requested_time_start ?? '',
                'requested_time_end' => $ticket->requested_time_end ?? '',
                'conductor_name' => $ticket->conductor_name ?? '',
                'conductor_phone' => $ticket->conductor_phone ?? '',
                
                // Timestamps importantes
                'created_at' => $ticket->created_at?->format('Y-m-d H:i:s') ?? null,
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s') ?? null,
                'rejected_at' => $ticket->rejected_at?->format('Y-m-d H:i:s') ?? null,
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s') ?? null,
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s') ?? null,
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s') ?? null,
                'rejection_reason' => $ticket->rejection_reason ?? '',
                
                // Calificaciones
                'service_rating' => $ticket->service_rating ?? null,
                'vehicle_rating' => $ticket->vehicle_rating ?? null,
                'rating_comments' => $ticket->rating_comments ?? '',
                
                // Usuario solicitante (el mismo que hace la petición)
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name ?? '',
                    'email' => $ticket->user->email ?? '',
                    'phone' => $ticket->user->phone ?? '',
                ] : null,
                
                // Vehículo asignado
                'vehicle' => $ticket->vehicle ? [
                    'id' => $ticket->vehicle->id,
                    'brand' => $ticket->vehicle->brand ?? '',
                    'model' => $ticket->vehicle->model ?? '',
                    'year' => $ticket->vehicle->year ?? 0,
                    'plates' => $ticket->vehicle->plates ?? '',
                    'internal_code' => $ticket->vehicle->internal_code ?? '',
                    'color' => $ticket->vehicle->color ?? '',
                    'vehicle_type' => $ticket->vehicle->vehicle_type ?? '',
                    'current_mileage' => $ticket->vehicle->current_mileage ?? 0,
                ] : null,
                
                // Despachador asignado
                'dispatcher' => $ticket->dispatcher ? [
                    'id' => $ticket->dispatcher->id,
                    'name' => $ticket->dispatcher->name ?? '',
                    'email' => $ticket->dispatcher->email ?? '',
                    'phone' => $ticket->dispatcher->phone ?? '',
                ] : null,
                
                // Aprobador
                'approver' => $ticket->approver ? [
                    'id' => $ticket->approver->id,
                    'name' => $ticket->approver->name ?? '',
                ] : null,
                
                // Licencia del conductor
                'conductor_license' => $ticket->conductorLicense ? [
                    'id' => $ticket->conductorLicense->id,
                    'license_number' => $ticket->conductorLicense->license_number ?? '',
                    'license_type' => $ticket->conductorLicense->license_type ?? '',
                    'expiry_date' => $ticket->conductorLicense->expiry_date?->format('Y-m-d') ?? '',
                    'full_name' => $ticket->conductorLicense->user?->name ?? $ticket->conductor_name ?? '',
                ] : null,
                
                // Checklists
                'checkout_checklist' => $this->formatChecklistForMobile($ticket, 'salida'),
                'checkin_checklist' => $this->formatChecklistForMobile($ticket, 'entrada'),
                
                // Estados booleanos para la UI
                'can_checkout' => $ticket->status === 'aprobado',
                'can_checkin' => $ticket->status === 'en_curso',
                'can_rate' => $ticket->status === 'completado' && !$ticket->service_rating,
                'has_checkout' => $ticket->checkoutChecklist !== null,
                'has_checkin' => $ticket->checkinChecklist !== null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Tickets obtenidos correctamente',
            'data' => [
                'tickets' => $formattedTickets,
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'from' => $tickets->firstItem(),
                    'to' => $tickets->lastItem(),
                    'has_more_pages' => $tickets->hasMorePages(),
                ]
            ]
        ], 200);
    }
}
