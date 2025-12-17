<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DriverLicense;
use App\Models\CheckoutChecklist;
use App\Models\CheckinChecklist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketsAndChecklistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creando tickets y checklists de prueba...\n";

        // Obtener usuarios necesarios
        $solicitante = User::whereHas('roles', function ($query) {
            $query->where('name', 'usuario');
        })->first();

        $despachador = User::whereHas('roles', function ($query) {
            $query->where('name', 'despachador');
        })->first();

        $encargado = User::whereHas('roles', function ($query) {
            $query->where('name', 'encargado');
        })->first();

        // Obtener vehículos disponibles
        $vehicles = Vehicle::limit(5)->get();

        if (!$solicitante || !$despachador || !$encargado || $vehicles->isEmpty()) {
            echo "⚠️  No hay suficientes usuarios o vehículos. Asegúrate de ejecutar los seeders previos.\n";
            echo "Solicitante: " . ($solicitante ? "✓" : "✗") . "\n";
            echo "Despachador: " . ($despachador ? "✓" : "✗") . "\n";
            echo "Encargado: " . ($encargado ? "✓" : "✗") . "\n";
            echo "Vehículos: " . $vehicles->count() . "\n";
            return;
        }

        // Crear licencias de conductor de prueba
        $licencias = [];
        foreach (['Juan Pérez', 'María González', 'Carlos López'] as $index => $nombre) {
            $licencias[] = DriverLicense::create([
                'user_id' => $solicitante->id,
                'license_number' => 'LIC' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'license_type' => 'C',
                'issue_date' => now()->subYears(3),
                'expiry_date' => now()->addYears(2),
            ]);
        }

        // ========== TICKET 1: COMPLETADO CON CHECKOUT Y CHECKIN ==========
        $ticket1 = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => 'REQ-001-2025',
            'user_id' => $solicitante->id,
            'vehicle_id' => $vehicles[0]->id,
            'dispatcher_id' => $despachador->id,
            'approved_by' => $encargado->id,
            'destination' => 'Ciudad de México - Oficinas centrales',
            'cliente' => 'Empresa ABC S.A. de C.V.',
            'purpose' => 'Reunión con clientes importantes',
            'requested_date' => now()->subDays(5),
            'requested_time_start' => '08:00',
            'requested_time_end' => '18:00',
            'conductor_name' => 'Juan Pérez',
            'conductor_phone' => '5551234567',
            'conductor_license_id' => $licencias[0]->id,
            'status' => 'completado',
            'approved_at' => now()->subDays(4),
            'checkout_at' => now()->subDays(4)->setTime(7, 30),
            'checkin_at' => now()->subDays(4)->setTime(18, 45),
            'completed_at' => now()->subDays(4)->setTime(18, 45),
        ]);

        // Checkout del ticket 1
        CheckoutChecklist::create([
            'ticket_id' => $ticket1->id,
            'fecha' => now()->subDays(4),
            'hora_salida' => '07:30',
            'kilometraje_inicial' => 45230.50,
            'nivel_combustible_inicial' => 'Lleno',
            // Llantas - todas en buen estado
            'llanta_delantera_derecha' => true,
            'llanta_delantera_izquierda' => true,
            'llanta_delantera_vida' => true,
            'llanta_trasera_derecha' => true,
            'llanta_trasera_izquierda' => true,
            'llanta_trasera_vida' => true,
            'llanta_refaccion' => true,
            'presion_adecuada' => true,
            // Frontal - todas en buen estado
            'parabrisas' => true,
            'cofre' => true,
            'parrilla' => true,
            'defensas' => true,
            'molduras' => true,
            'placa' => true,
            'salpicadera' => true,
            'antena' => true,
            // Luces - todas funcionando
            'intermitentes' => true,
            'direccional_derecha' => true,
            'direccional_izquierda' => true,
            'luz_stop' => true,
            'faros' => true,
            'luces_altas' => true,
            'luz_interior' => true,
            'calaveras_buen_estado' => true,
            // Seguridad - todo presente
            'mata_chispas' => true,
            'alarma' => true,
            'extintor' => true,
            'botiquin' => true,
            'tarjeta_circulacion' => true,
            'licencia_conducir_vigente' => true,
            'poliza_seguro' => true,
            'triangulo_emergencia' => true,
            // Interior - todo en buen estado
            'tablero_indicadores' => true,
            'switch_encendido' => true,
            'controles_ac' => true,
            'defroster' => true,
            'radio' => true,
            'volante' => true,
            'bolsas_aire' => true,
            'cinturon_seguridad' => true,
            'coderas' => true,
            'espejo_interior' => true,
            'freno_mano' => true,
            'encendedor' => true,
            'guantera' => true,
            'manijas_interiores' => true,
            'seguros' => true,
            'asientos' => true,
            'tapetes_delanteros_traseros' => true,
            // Motor
            'nivel_aceite_motor' => true,
            'nivel_anticongelante' => true,
            'nivel_liquido_frenos' => true,
            'bateria' => true,
            'bayoneta_aceite_motor' => true,
            'tapones' => true,
            'bocina_claxon' => true,
            'radiador' => true,
            // Herramienta
            'gato' => true,
            'llave_ruedas' => true,
            'cables_pasa_corriente' => true,
            'caja_bolsa_herramientas' => true,
            'dado_birlo_seguridad' => true,
            // Calcomanías
            'calcomanias_permisos' => true,
            'calcomania_velocidad_maxima' => true,
            // Observaciones
            'mantenimiento_preventivo' => 'Próximo servicio en 5,000 km',
            'mantenimiento_correctivo' => 'N/A',
            'condicion_carroceria_log' => json_encode([]),
            'responsable_recibo_uso' => 'Juan Pérez',
            'responsable_entrega' => $despachador->name,
        ]);

        // Checkin del ticket 1
        CheckinChecklist::create([
            'ticket_id' => $ticket1->id,
            'fecha' => now()->subDays(4),
            'hora_salida' => '08:00',
            'hora_entrada' => '18:45',
            'kilometraje_inicial' => 45230.50,
            'kilometraje_final' => 45478.30,
            'nivel_combustible_inicial' => 'Lleno',
            'nivel_combustible_final' => '1/2',
            // Llantas - todas en buen estado
            'llanta_delantera_derecha' => true,
            'llanta_delantera_izquierda' => true,
            'llanta_delantera_vida' => true,
            'llanta_trasera_derecha' => true,
            'llanta_trasera_izquierda' => true,
            'llanta_trasera_vida' => true,
            'llanta_refaccion' => true,
            'presion_adecuada' => true,
            // Frontal - todas en buen estado
            'parabrisas' => true,
            'cofre' => true,
            'parrilla' => true,
            'defensas' => true,
            'molduras' => true,
            'placa' => true,
            'salpicadera' => true,
            'antena' => true,
            // Luces
            'intermitentes' => true,
            'direccional_derecha' => true,
            'direccional_izquierda' => true,
            'luz_stop' => true,
            'faros' => true,
            'luces_altas' => true,
            'luz_interior' => true,
            'calaveras_buen_estado' => true,
            // Seguridad
            'mata_chispas' => true,
            'alarma' => true,
            'extintor' => true,
            'botiquin' => true,
            'tarjeta_circulacion' => true,
            'licencia_conducir_vigente' => true,
            'poliza_seguro' => true,
            'triangulo_emergencia' => true,
            // Interior
            'tablero_indicadores' => true,
            'switch_encendido' => true,
            'controles_ac' => true,
            'defroster' => true,
            'radio' => true,
            'volante' => true,
            'bolsas_aire' => true,
            'cinturon_seguridad' => true,
            'coderas' => true,
            'espejo_interior' => true,
            'freno_mano' => true,
            'encendedor' => true,
            'guantera' => true,
            'manijas_interiores' => true,
            'seguros' => true,
            'asientos' => true,
            'tapetes_delanteros_traseros' => true,
            // Motor
            'nivel_aceite_motor' => true,
            'nivel_anticongelante' => true,
            'nivel_liquido_frenos' => true,
            'bateria' => true,
            'bayoneta_aceite_motor' => true,
            'tapones' => true,
            'bocina_claxon' => true,
            'radiador' => true,
            // Herramienta
            'gato' => true,
            'llave_ruedas' => true,
            'cables_pasa_corriente' => true,
            'caja_bolsa_herramientas' => true,
            'dado_birlo_seguridad' => true,
            // Calcomanías
            'calcomanias_permisos' => true,
            'calcomania_velocidad_maxima' => true,
            // Observaciones
            'mantenimiento_preventivo' => 'Realizar cambio de aceite en próximo servicio',
            'mantenimiento_correctivo' => 'Rayón menor en puerta trasera izquierda',
            'condicion_carroceria_log' => json_encode([
                ['tipo' => 'rayon', 'ubicacion' => 'puerta_trasera_izquierda', 'gravedad' => 'leve']
            ]),
            'responsable_recibo_uso' => 'Juan Pérez',
            'responsable_entrega' => $despachador->name,
        ]);

        // ========== TICKET 2: EN CURSO CON CHECKOUT ==========
        $ticket2 = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => 'REQ-002-2025',
            'user_id' => $solicitante->id,
            'vehicle_id' => $vehicles[1]->id,
            'dispatcher_id' => $despachador->id,
            'approved_by' => $encargado->id,
            'destination' => 'Guadalajara - Planta de producción',
            'cliente' => 'Manufactura Industrial S.A.',
            'purpose' => 'Supervisión de calidad y producción',
            'requested_date' => now(),
            'requested_time_start' => '06:00',
            'requested_time_end' => '20:00',
            'conductor_name' => 'María González',
            'conductor_phone' => '5559876543',
            'conductor_license_id' => $licencias[1]->id,
            'status' => 'en_curso',
            'approved_at' => now()->subHours(3),
            'checkout_at' => now()->subHours(2),
        ]);

        // Checkout del ticket 2
        CheckoutChecklist::create([
            'ticket_id' => $ticket2->id,
            'fecha' => now(),
            'hora_salida' => '06:00',
            'kilometraje_inicial' => 28450.00,
            'nivel_combustible_inicial' => 'Lleno',
            // Llantas
            'llanta_delantera_derecha' => true,
            'llanta_delantera_izquierda' => true,
            'llanta_delantera_vida' => true,
            'llanta_trasera_derecha' => true,
            'llanta_trasera_izquierda' => true,
            'llanta_trasera_vida' => true,
            'llanta_refaccion' => true,
            'presion_adecuada' => true,
            // Frontal
            'parabrisas' => true,
            'cofre' => true,
            'parrilla' => true,
            'defensas' => true,
            'molduras' => true,
            'placa' => true,
            'salpicadera' => true,
            'antena' => true,
            // Luces
            'intermitentes' => true,
            'direccional_derecha' => true,
            'direccional_izquierda' => true,
            'luz_stop' => true,
            'faros' => true,
            'luces_altas' => true,
            'luz_interior' => true,
            'calaveras_buen_estado' => true,
            // Seguridad
            'mata_chispas' => true,
            'alarma' => true,
            'extintor' => true,
            'botiquin' => true,
            'tarjeta_circulacion' => true,
            'licencia_conducir_vigente' => true,
            'poliza_seguro' => true,
            'triangulo_emergencia' => true,
            // Interior
            'tablero_indicadores' => true,
            'switch_encendido' => true,
            'controles_ac' => true,
            'defroster' => true,
            'radio' => true,
            'volante' => true,
            'bolsas_aire' => true,
            'cinturon_seguridad' => true,
            'coderas' => true,
            'espejo_interior' => true,
            'freno_mano' => true,
            'encendedor' => true,
            'guantera' => true,
            'manijas_interiores' => true,
            'seguros' => true,
            'asientos' => true,
            'tapetes_delanteros_traseros' => true,
            // Motor
            'nivel_aceite_motor' => true,
            'nivel_anticongelante' => true,
            'nivel_liquido_frenos' => true,
            'bateria' => true,
            'bayoneta_aceite_motor' => true,
            'tapones' => true,
            'bocina_claxon' => true,
            'radiador' => true,
            // Herramienta
            'gato' => true,
            'llave_ruedas' => true,
            'cables_pasa_corriente' => true,
            'caja_bolsa_herramientas' => true,
            'dado_birlo_seguridad' => true,
            // Calcomanías
            'calcomanias_permisos' => true,
            'calcomania_velocidad_maxima' => true,
            // Observaciones
            'mantenimiento_preventivo' => 'Vehículo en óptimas condiciones',
            'mantenimiento_correctivo' => 'N/A',
            'condicion_carroceria_log' => json_encode([]),
            'responsable_recibo_uso' => 'María González',
            'responsable_entrega' => $despachador->name,
        ]);

        // ========== TICKET 3: APROBADO SIN CHECKOUT ==========
        $ticket3 = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => 'REQ-003-2025',
            'user_id' => $solicitante->id,
            'vehicle_id' => $vehicles[2]->id,
            'dispatcher_id' => $despachador->id,
            'approved_by' => $encargado->id,
            'destination' => 'Monterrey - Centro de distribución',
            'cliente' => 'Logística del Norte S.A.',
            'purpose' => 'Entrega de documentación y revisión de inventario',
            'requested_date' => now()->addDays(1),
            'requested_time_start' => '09:00',
            'requested_time_end' => '16:00',
            'conductor_name' => 'Carlos López',
            'conductor_phone' => '5552345678',
            'conductor_license_id' => $licencias[2]->id,
            'status' => 'aprobado',
            'approved_at' => now()->subMinutes(30),
        ]);

        // ========== TICKET 4: PENDIENTE ==========
        $ticket4 = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => 'REQ-004-2025',
            'user_id' => $solicitante->id,
            'vehicle_id' => $vehicles[3]->id,
            'destination' => 'Querétaro - Oficinas regionales',
            'cliente' => 'Servicios Corporativos QRO',
            'purpose' => 'Reunión de capacitación y actualización de sistemas',
            'requested_date' => now()->addDays(2),
            'requested_time_start' => '08:30',
            'requested_time_end' => '17:30',
            'conductor_name' => 'Juan Pérez',
            'conductor_phone' => '5551234567',
            'conductor_license_id' => $licencias[0]->id,
            'status' => 'pendiente',
        ]);

        // ========== TICKET 5: COMPLETADO CON CHECKOUT Y CHECKIN (CON PROBLEMAS) ==========
        $ticket5 = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => 'REQ-005-2025',
            'user_id' => $solicitante->id,
            'vehicle_id' => $vehicles[4]->id,
            'dispatcher_id' => $despachador->id,
            'approved_by' => $encargado->id,
            'destination' => 'Puebla - Almacén general',
            'cliente' => 'Distribuidora del Centro S.A.',
            'purpose' => 'Levantamiento de inventario anual',
            'requested_date' => now()->subDays(2),
            'requested_time_start' => '07:00',
            'requested_time_end' => '15:00',
            'conductor_name' => 'María González',
            'conductor_phone' => '5559876543',
            'conductor_license_id' => $licencias[1]->id,
            'status' => 'completado',
            'approved_at' => now()->subDays(2),
            'checkout_at' => now()->subDays(2)->setTime(6, 45),
            'checkin_at' => now()->subDays(2)->setTime(15, 30),
            'completed_at' => now()->subDays(2)->setTime(15, 30),
        ]);

        // Checkout del ticket 5
        CheckoutChecklist::create([
            'ticket_id' => $ticket5->id,
            'fecha' => now()->subDays(2),
            'hora_salida' => '06:45',
            'kilometraje_inicial' => 62150.00,
            'nivel_combustible_inicial' => '3/4',
            // Llantas
            'llanta_delantera_derecha' => true,
            'llanta_delantera_izquierda' => true,
            'llanta_delantera_vida' => true,
            'llanta_trasera_derecha' => true,
            'llanta_trasera_izquierda' => false, // Problema detectado
            'llanta_trasera_vida' => true,
            'llanta_refaccion' => true,
            'presion_adecuada' => false, // Problema detectado
            // Frontal
            'parabrisas' => true,
            'cofre' => true,
            'parrilla' => true,
            'defensas' => true,
            'molduras' => true,
            'placa' => true,
            'salpicadera' => true,
            'antena' => true,
            // Luces
            'intermitentes' => true,
            'direccional_derecha' => true,
            'direccional_izquierda' => true,
            'luz_stop' => true,
            'faros' => true,
            'luces_altas' => true,
            'luz_interior' => false, // Problema detectado
            'calaveras_buen_estado' => true,
            // Seguridad
            'mata_chispas' => true,
            'alarma' => true,
            'extintor' => true,
            'botiquin' => true,
            'tarjeta_circulacion' => true,
            'licencia_conducir_vigente' => true,
            'poliza_seguro' => true,
            'triangulo_emergencia' => true,
            // Interior
            'tablero_indicadores' => true,
            'switch_encendido' => true,
            'controles_ac' => true,
            'defroster' => true,
            'radio' => true,
            'volante' => true,
            'bolsas_aire' => true,
            'cinturon_seguridad' => true,
            'coderas' => true,
            'espejo_interior' => true,
            'freno_mano' => true,
            'encendedor' => true,
            'guantera' => true,
            'manijas_interiores' => true,
            'seguros' => true,
            'asientos' => true,
            'tapetes_delanteros_traseros' => true,
            // Motor
            'nivel_aceite_motor' => true,
            'nivel_anticongelante' => true,
            'nivel_liquido_frenos' => true,
            'bateria' => true,
            'bayoneta_aceite_motor' => true,
            'tapones' => true,
            'bocina_claxon' => true,
            'radiador' => true,
            // Herramienta
            'gato' => true,
            'llave_ruedas' => true,
            'cables_pasa_corriente' => true,
            'caja_bolsa_herramientas' => true,
            'dado_birlo_seguridad' => true,
            // Calcomanías
            'calcomanias_permisos' => true,
            'calcomania_velocidad_maxima' => true,
            // Observaciones
            'mantenimiento_preventivo' => 'Revisar presión de llantas',
            'mantenimiento_correctivo' => 'Llanta trasera izquierda necesita reemplazo. Luz interior fundida.',
            'condicion_carroceria_log' => json_encode([
                ['tipo' => 'abolladura', 'ubicacion' => 'puerta_delantera_derecha', 'gravedad' => 'leve']
            ]),
            'responsable_recibo_uso' => 'María González',
            'responsable_entrega' => $despachador->name,
        ]);

        // Checkin del ticket 5
        CheckinChecklist::create([
            'ticket_id' => $ticket5->id,
            'fecha' => now()->subDays(2),
            'hora_salida' => '09:00',
            'hora_entrada' => '15:30',
            'kilometraje_inicial' => 62150.00,
            'kilometraje_final' => 62298.50,
            'nivel_combustible_inicial' => '3/4',
            'nivel_combustible_final' => '1/4',
            // Llantas
            'llanta_delantera_derecha' => true,
            'llanta_delantera_izquierda' => true,
            'llanta_delantera_vida' => true,
            'llanta_trasera_derecha' => true,
            'llanta_trasera_izquierda' => false, // Sigue con problema
            'llanta_trasera_vida' => true,
            'llanta_refaccion' => true,
            'presion_adecuada' => false, // Sigue con problema
            // Frontal
            'parabrisas' => false, // Nuevo problema - piedra impactó
            'cofre' => true,
            'parrilla' => true,
            'defensas' => true,
            'molduras' => true,
            'placa' => true,
            'salpicadera' => true,
            'antena' => true,
            // Luces
            'intermitentes' => true,
            'direccional_derecha' => true,
            'direccional_izquierda' => true,
            'luz_stop' => true,
            'faros' => true,
            'luces_altas' => true,
            'luz_interior' => false, // Sigue con problema
            'calaveras_buen_estado' => true,
            // Seguridad
            'mata_chispas' => true,
            'alarma' => true,
            'extintor' => true,
            'botiquin' => true,
            'tarjeta_circulacion' => true,
            'licencia_conducir_vigente' => true,
            'poliza_seguro' => true,
            'triangulo_emergencia' => true,
            // Interior
            'tablero_indicadores' => true,
            'switch_encendido' => true,
            'controles_ac' => true,
            'defroster' => true,
            'radio' => true,
            'volante' => true,
            'bolsas_aire' => true,
            'cinturon_seguridad' => true,
            'coderas' => true,
            'espejo_interior' => true,
            'freno_mano' => true,
            'encendedor' => true,
            'guantera' => true,
            'manijas_interiores' => true,
            'seguros' => true,
            'asientos' => true,
            'tapetes_delanteros_traseros' => true,
            // Motor
            'nivel_aceite_motor' => true,
            'nivel_anticongelante' => true,
            'nivel_liquido_frenos' => true,
            'bateria' => true,
            'bayoneta_aceite_motor' => true,
            'tapones' => true,
            'bocina_claxon' => true,
            'radiador' => true,
            // Herramienta
            'gato' => true,
            'llave_ruedas' => true,
            'cables_pasa_corriente' => true,
            'caja_bolsa_herramientas' => true,
            'dado_birlo_seguridad' => true,
            // Calcomanías
            'calcomanias_permisos' => true,
            'calcomania_velocidad_maxima' => true,
            // Observaciones
            'mantenimiento_preventivo' => 'Programar servicio completo',
            'mantenimiento_correctivo' => 'URGENTE: Reemplazar llanta trasera izquierda, reparar parabrisas (grieta por piedra), cambiar foco luz interior',
            'condicion_carroceria_log' => json_encode([
                ['tipo' => 'abolladura', 'ubicacion' => 'puerta_delantera_derecha', 'gravedad' => 'leve'],
                ['tipo' => 'grieta', 'ubicacion' => 'parabrisas', 'gravedad' => 'media', 'nuevo' => true]
            ]),
            'responsable_recibo_uso' => 'María González',
            'responsable_entrega' => $despachador->name,
        ]);

        echo "\n✅ Tickets y checklists de prueba creados exitosamente!\n";
        echo "   - Ticket 1: Completado con checkout y checkin (todo OK)\n";
        echo "   - Ticket 2: En curso con checkout (esperando checkin)\n";
        echo "   - Ticket 3: Aprobado (listo para checkout)\n";
        echo "   - Ticket 4: Pendiente (esperando aprobación)\n";
        echo "   - Ticket 5: Completado con problemas detectados\n";
    }
}
