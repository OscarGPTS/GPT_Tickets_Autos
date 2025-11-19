<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'tipo_inspeccion',
        'folio',
        'fecha',
        'destino',
        'modelo',
        'placas',
        'marca',
        'hora_salida',
        'hora_entrada',
        'kilometraje_inicial',
        'kilometraje_final',
        'nivel_combustible_inicial',
        'nivel_combustible_final',
        // Llantas
        'llanta_delantera_derecha',
        'llanta_delantera_izquierda',
        'llanta_trasera_derecha',
        'llanta_trasera_izquierda',
        'llanta_refaccion',
        'presion_adecuada',
        // Frontal
        'parabrisas',
        'cofre',
        'parrilla',
        'defensas',
        'molduras',
        'placa',
        'salpicadera',
        'antena',
        // Luces
        'intermitentes',
        'direccional_derecha',
        'direccional_izquierda',
        'luz_stop',
        'faros',
        'luces_altas',
        'luz_interior',
        'calaveras_buen_estado',
        // Seguridad
        'mata_chispas',
        'alarma',
        'extintor',
        'botiquin',
        'tarjeta_circulacion',
        'licencia_conducir_vigente',
        'poliza_seguro',
        'triangulo_emergencia',
        // Interior
        'tablero_indicadores',
        'switch_encendido',
        'controles_ac',
        'defroster',
        'radio',
        'volante',
        'bolsas_aire',
        'cinturon_seguridad',
        'coderas',
        'espejo_interior',
        'freno_mano',
        'encendedor',
        'guantera',
        'manijas_interiores',
        'seguros',
        'asientos',
        'tapetes_delanteros_traseros',
        // Motor
        'nivel_aceite_motor',
        'nivel_anticongelante',
        'nivel_liquido_frenos',
        'bateria',
        'bayoneta_aceite_motor',
        'tapones',
        'bocina_claxon',
        'radiador',
        // Herramienta
        'gato',
        'llave_ruedas',
        'cables_pasa_corriente',
        'caja_bolsa_herramientas',
        'dado_birlo_seguridad',
        // Calcomanías
        'calcomanias_permisos',
        'calcomania_velocidad_maxima',
        // Observaciones
        'mantenimiento_preventivo',
        'mantenimiento_correctivo',
        'condicion_carroceria_log',
        'responsable_recibo_uso',
        'responsable_entrega',
    ];

    protected $casts = [
        'fecha' => 'date',
        'kilometraje_inicial' => 'decimal:2',
        'kilometraje_final' => 'decimal:2',
        'condicion_carroceria_log' => 'array',
        // Todos los campos boolean
        'llanta_delantera_derecha' => 'boolean',
        'llanta_delantera_izquierda' => 'boolean',
        'llanta_trasera_derecha' => 'boolean',
        'llanta_trasera_izquierda' => 'boolean',
        'llanta_refaccion' => 'boolean',
        'presion_adecuada' => 'boolean',
        'parabrisas' => 'boolean',
        'cofre' => 'boolean',
        'parrilla' => 'boolean',
        'defensas' => 'boolean',
        'molduras' => 'boolean',
        'placa' => 'boolean',
        'salpicadera' => 'boolean',
        'antena' => 'boolean',
        'intermitentes' => 'boolean',
        'direccional_derecha' => 'boolean',
        'direccional_izquierda' => 'boolean',
        'luz_stop' => 'boolean',
        'faros' => 'boolean',
        'luces_altas' => 'boolean',
        'luz_interior' => 'boolean',
        'calaveras_buen_estado' => 'boolean',
        'mata_chispas' => 'boolean',
        'alarma' => 'boolean',
        'extintor' => 'boolean',
        'botiquin' => 'boolean',
        'tarjeta_circulacion' => 'boolean',
        'licencia_conducir_vigente' => 'boolean',
        'poliza_seguro' => 'boolean',
        'triangulo_emergencia' => 'boolean',
        'tablero_indicadores' => 'boolean',
        'switch_encendido' => 'boolean',
        'controles_ac' => 'boolean',
        'defroster' => 'boolean',
        'radio' => 'boolean',
        'volante' => 'boolean',
        'bolsas_aire' => 'boolean',
        'cinturon_seguridad' => 'boolean',
        'coderas' => 'boolean',
        'espejo_interior' => 'boolean',
        'freno_mano' => 'boolean',
        'encendedor' => 'boolean',
        'guantera' => 'boolean',
        'manijas_interiores' => 'boolean',
        'seguros' => 'boolean',
        'asientos' => 'boolean',
        'tapetes_delanteros_traseros' => 'boolean',
        'nivel_aceite_motor' => 'boolean',
        'nivel_anticongelante' => 'boolean',
        'nivel_liquido_frenos' => 'boolean',
        'bateria' => 'boolean',
        'bayoneta_aceite_motor' => 'boolean',
        'tapones' => 'boolean',
        'bocina_claxon' => 'boolean',
        'radiador' => 'boolean',
        'gato' => 'boolean',
        'llave_ruedas' => 'boolean',
        'cables_pasa_corriente' => 'boolean',
        'caja_bolsa_herramientas' => 'boolean',
        'dado_birlo_seguridad' => 'boolean',
        'calcomanias_permisos' => 'boolean',
        'calcomania_velocidad_maxima' => 'boolean',
    ];

    /**
     * Ticket al que pertenece el checklist
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Obtener los ítems con problemas (false)
     */
    public function getProblemsAttribute(): array
    {
        $problems = [];
        
        foreach ($this->attributes as $key => $value) {
            if ($this->isChecklistItem($key) && !$value) {
                $problems[] = $this->formatFieldName($key);
            }
        }
        
        return $problems;
    }

    /**
     * Verificar si un campo es un ítem del checklist
     */
    private function isChecklistItem(string $field): bool
    {
        $excludedFields = [
            'id', 'ticket_id', 'tipo_inspeccion', 'folio', 'fecha', 'destino',
            'modelo', 'placas', 'marca', 'hora_salida', 'hora_entrada',
            'kilometraje_inicial', 'kilometraje_final', 'nivel_combustible_inicial',
            'nivel_combustible_final', 'mantenimiento_preventivo', 'mantenimiento_correctivo',
            'condicion_carroceria_log', 'responsable_recibo_uso', 'responsable_entrega',
            'created_at', 'updated_at'
        ];
        
        return !in_array($field, $excludedFields) && 
               isset($this->casts[$field]) && 
               $this->casts[$field] === 'boolean';
    }

    /**
     * Formatear nombre de campo para lectura humana
     */
    private function formatFieldName(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}
