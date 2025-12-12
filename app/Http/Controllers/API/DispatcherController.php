<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DispatcherController extends Controller
{
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

        // Verificar que el usuario tenga el rol de despachador
        $isDispatcher = $user->roles()->where('name', 'despachador')->exists();

        if (!$isDispatcher) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene permisos de despachador'
            ], 403);
        }

        // Obtener todos los tickets asignados al despachador
        // Ordenados por más reciente primero
        $tickets = Ticket::with([
            'user:id,name,email,phone',
            'vehicle:id,brand,model,year,plates,vehicle_number',
            'conductorLicense:id,license_number,full_name,expiration_date',
            'checklists'
        ])
        ->where('dispatcher_id', $user->id)
        ->whereIn('status', ['aprobado', 'en_progreso'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'folio' => $ticket->folio,
                'requisicion' => $ticket->requisicion,
                'status' => $ticket->status,
                'destination' => $ticket->destination,
                'cliente' => $ticket->cliente,
                'purpose' => $ticket->purpose,
                'requested_date' => $ticket->requested_date?->format('Y-m-d'),
                'requested_time_start' => $ticket->requested_time_start,
                'requested_time_end' => $ticket->requested_time_end,
                'conductor_name' => $ticket->conductor_name,
                'conductor_phone' => $ticket->conductor_phone,
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s'),
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s'),
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s'),
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s'),
                
                // Usuario solicitante
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                    'phone' => $ticket->user->phone,
                ] : null,
                
                // Vehículo asignado
                'vehicle' => $ticket->vehicle ? [
                    'id' => $ticket->vehicle->id,
                    'brand' => $ticket->vehicle->brand,
                    'model' => $ticket->vehicle->model,
                    'year' => $ticket->vehicle->year,
                    'plates' => $ticket->vehicle->plates,
                    'vehicle_number' => $ticket->vehicle->vehicle_number,
                ] : null,
                
                // Licencia del conductor
                'conductor_license' => $ticket->conductorLicense ? [
                    'id' => $ticket->conductorLicense->id,
                    'license_number' => $ticket->conductorLicense->license_number,
                    'full_name' => $ticket->conductorLicense->full_name,
                    'expiration_date' => $ticket->conductorLicense->expiration_date?->format('Y-m-d'),
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
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'fecha' => 'required|date',
            'hora_salida' => 'required',
            'kilometraje_inicial' => 'required|numeric',
            'nivel_combustible_inicial' => 'required|string',
            // Los demás campos son opcionales pero validarlos si vienen
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // Verificar que el ticket esté en estado aprobado
            if (!in_array($ticket->status, ['aprobado', 'en_progreso'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El ticket debe estar aprobado para crear un checklist'
                ], 400);
            }

            // Buscar si ya existe un checklist de salida
            $checklist = Checklist::where('ticket_id', $request->ticket_id)
                                  ->where('tipo_inspeccion', 'salida')
                                  ->first();

            $data = $this->prepareChecklistData($request, $ticket, 'salida');

            if ($checklist) {
                // Actualizar checklist existente
                $checklist->update($data);
            } else {
                // Crear nuevo checklist
                $checklist = Checklist::create($data);
                
                // Actualizar el ticket con la fecha de checkout
                $ticket->update([
                    'checkout_at' => now(),
                    'status' => 'en_progreso'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $checklist->wasRecentlyCreated ? 'Checklist de salida creado correctamente' : 'Checklist de salida actualizado correctamente',
                'data' => [
                    'checklist' => $checklist,
                    'ticket' => $ticket->fresh(['vehicle', 'user'])
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear o actualizar checklist de entrada (checkin)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkinChecklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'required',
            'kilometraje_final' => 'required|numeric',
            'nivel_combustible_final' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // Verificar que el ticket esté en progreso
            if ($ticket->status !== 'en_progreso') {
                return response()->json([
                    'success' => false,
                    'message' => 'El ticket debe estar en progreso para crear un checklist de entrada'
                ], 400);
            }

            // Verificar que exista un checklist de salida
            $checkoutExists = Checklist::where('ticket_id', $request->ticket_id)
                                       ->where('tipo_inspeccion', 'salida')
                                       ->exists();

            if (!$checkoutExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe existir un checklist de salida antes de crear uno de entrada'
                ], 400);
            }

            // Buscar si ya existe un checklist de entrada
            $checklist = Checklist::where('ticket_id', $request->ticket_id)
                                  ->where('tipo_inspeccion', 'entrada')
                                  ->first();

            $data = $this->prepareChecklistData($request, $ticket, 'entrada');

            if ($checklist) {
                // Actualizar checklist existente
                $checklist->update($data);
            } else {
                // Crear nuevo checklist
                $checklist = Checklist::create($data);
                
                // Actualizar el ticket con la fecha de checkin
                $ticket->update([
                    'checkin_at' => now(),
                    'status' => 'completado',
                    'completed_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $checklist->wasRecentlyCreated ? 'Checklist de entrada creado correctamente' : 'Checklist de entrada actualizado correctamente',
                'data' => [
                    'checklist' => $checklist,
                    'ticket' => $ticket->fresh(['vehicle', 'user'])
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el checklist',
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
            'vehicle:id,brand,model,year,plates,vehicle_number',
            'conductorLicense:id,license_number,full_name,expiration_date',
            'checklists'
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
                'folio' => $ticket->folio,
                'requisicion' => $ticket->requisicion,
                'status' => $ticket->status,
                'destination' => $ticket->destination,
                'cliente' => $ticket->cliente,
                'purpose' => $ticket->purpose,
                'requested_date' => $ticket->requested_date?->format('Y-m-d'),
                'requested_time_start' => $ticket->requested_time_start,
                'requested_time_end' => $ticket->requested_time_end,
                'conductor_name' => $ticket->conductor_name,
                'conductor_phone' => $ticket->conductor_phone,
                'approved_at' => $ticket->approved_at?->format('Y-m-d H:i:s'),
                'checkout_at' => $ticket->checkout_at?->format('Y-m-d H:i:s'),
                'checkin_at' => $ticket->checkin_at?->format('Y-m-d H:i:s'),
                'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s'),
                
                'user' => $ticket->user,
                'vehicle' => $ticket->vehicle,
                'conductor_license' => $ticket->conductorLicense,
                'checkout_checklist' => $this->formatChecklistForMobile($ticket, 'salida'),
                'checkin_checklist' => $this->formatChecklistForMobile($ticket, 'entrada'),
            ]
        ], 200);
    }

    /**
     * Preparar datos del checklist desde el request
     * 
     * @param Request $request
     * @param Ticket $ticket
     * @param string $tipo
     * @return array
     */
    private function prepareChecklistData(Request $request, Ticket $ticket, string $tipo): array
    {
        $data = [
            'ticket_id' => $ticket->id,
            'tipo_inspeccion' => $tipo,
            'folio' => $ticket->folio,
            'fecha' => $request->fecha,
            'destino' => $request->destino ?? $ticket->destination,
        ];

        // Datos del vehículo
        if ($ticket->vehicle) {
            $data['modelo'] = $ticket->vehicle->model;
            $data['placas'] = $ticket->vehicle->plates;
            $data['marca'] = $ticket->vehicle->brand;
        }

        // Agregar todos los campos del request excepto ticket_id
        $fillableFields = (new Checklist())->getFillable();
        
        foreach ($request->all() as $key => $value) {
            if ($key !== 'ticket_id' && in_array($key, $fillableFields)) {
                // Convertir strings "true"/"false" a booleanos
                if (is_string($value) && ($value === 'true' || $value === 'false')) {
                    $data[$key] = $value === 'true';
                } else {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
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
        $checklist = $ticket->checklists->where('tipo_inspeccion', $tipo)->first();

        if (!$checklist) {
            // Retornar estructura vacía con valores por defecto
            return [
                'id' => null,
                'exists' => false,
                'tipo_inspeccion' => $tipo,
                'folio' => $ticket->folio,
                'fecha' => null,
                'destino' => $ticket->destination,
                'modelo' => $ticket->vehicle?->model,
                'placas' => $ticket->vehicle?->plates,
                'marca' => $ticket->vehicle?->brand,
                'hora_salida' => null,
                'hora_entrada' => null,
                'kilometraje_inicial' => null,
                'kilometraje_final' => null,
                'nivel_combustible_inicial' => null,
                'nivel_combustible_final' => null,
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
            'tipo_inspeccion' => $checklist->tipo_inspeccion,
            'folio' => $checklist->folio,
            'fecha' => $checklist->fecha?->format('Y-m-d'),
            'destino' => $checklist->destino,
            'modelo' => $checklist->modelo,
            'placas' => $checklist->placas,
            'marca' => $checklist->marca,
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
            'llanta_delantera_derecha' => null,
            'llanta_delantera_izquierda' => null,
            'llanta_delantera_vida' => null,
            'llanta_trasera_derecha' => null,
            'llanta_trasera_izquierda' => null,
            'llanta_trasera_vida' => null,
            'llanta_refaccion' => null,
            'presion_adecuada' => null,
        ];
    }

    private function getEmptyFrontalSection(): array
    {
        return [
            'parabrisas' => null,
            'cofre' => null,
            'parrilla' => null,
            'defensas' => null,
            'molduras' => null,
            'placa' => null,
            'salpicadera' => null,
            'antena' => null,
        ];
    }

    private function getEmptyLucesSection(): array
    {
        return [
            'intermitentes' => null,
            'direccional_derecha' => null,
            'direccional_izquierda' => null,
            'luz_stop' => null,
            'faros' => null,
            'luces_altas' => null,
            'luz_interior' => null,
            'calaveras_buen_estado' => null,
        ];
    }

    private function getEmptySeguridadSection(): array
    {
        return [
            'mata_chispas' => null,
            'alarma' => null,
            'extintor' => null,
            'botiquin' => null,
            'tarjeta_circulacion' => null,
            'licencia_conducir_vigente' => null,
            'poliza_seguro' => null,
            'triangulo_emergencia' => null,
        ];
    }

    private function getEmptyInteriorSection(): array
    {
        return [
            'tablero_indicadores' => null,
            'switch_encendido' => null,
            'controles_ac' => null,
            'defroster' => null,
            'radio' => null,
            'volante' => null,
            'bolsas_aire' => null,
            'cinturon_seguridad' => null,
            'coderas' => null,
            'espejo_interior' => null,
            'freno_mano' => null,
            'encendedor' => null,
            'guantera' => null,
            'manijas_interiores' => null,
            'seguros' => null,
            'asientos' => null,
            'tapetes_delanteros_traseros' => null,
        ];
    }

    private function getEmptyMotorSection(): array
    {
        return [
            'nivel_aceite_motor' => null,
            'nivel_anticongelante' => null,
            'nivel_liquido_frenos' => null,
            'bateria' => null,
            'bayoneta_aceite_motor' => null,
            'tapones' => null,
            'bocina_claxon' => null,
            'radiador' => null,
        ];
    }

    private function getEmptyHerramientaSection(): array
    {
        return [
            'gato' => null,
            'llave_ruedas' => null,
            'cables_pasa_corriente' => null,
            'caja_bolsa_herramientas' => null,
            'dado_birlo_seguridad' => null,
        ];
    }

    private function getEmptyCalcomaniasSection(): array
    {
        return [
            'calcomanias_permisos' => null,
            'calcomania_velocidad_maxima' => null,
        ];
    }

    private function getEmptyObservacionesSection(): array
    {
        return [
            'mantenimiento_preventivo' => null,
            'mantenimiento_correctivo' => null,
            'condicion_carroceria_log' => null,
            'condicion_carroceria_imagen' => null,
            'responsable_recibo_uso' => null,
            'responsable_entrega' => null,
        ];
    }
}
