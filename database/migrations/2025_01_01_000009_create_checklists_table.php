<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_inspeccion', ['salida', 'entrada']);
            $table->bigInteger('folio');
            $table->date('fecha');
            $table->string('destino');
            $table->string('modelo');
            $table->string('placas');
            $table->string('marca');
            
            // Tiempos y kilometraje
            $table->time('hora_salida')->nullable();
            $table->time('hora_entrada')->nullable();
            $table->decimal('kilometraje_inicial', 20, 2);
            $table->decimal('kilometraje_final', 20, 2)->nullable();
            $table->string('nivel_combustible_inicial');
            $table->string('nivel_combustible_final')->nullable();
            
            // LLANTAS (Boolean: TRUE = Buen Estado)
            $table->boolean('llanta_delantera_derecha')->default(true);
            $table->boolean('llanta_delantera_izquierda')->default(true);
            $table->boolean('llanta_delantera_vida')->default(true);
            $table->boolean('llanta_trasera_derecha')->default(true);
            $table->boolean('llanta_trasera_izquierda')->default(true);
            $table->boolean('llanta_trasera_vida')->default(true);
            $table->boolean('llanta_refaccion')->default(true);
            $table->boolean('presion_adecuada')->default(true);
            
            // FRONTAL
            $table->boolean('parabrisas')->default(true);
            $table->boolean('cofre')->default(true);
            $table->boolean('parrilla')->default(true);
            $table->boolean('defensas')->default(true);
            $table->boolean('molduras')->default(true);
            $table->boolean('placa')->default(true);
            $table->boolean('salpicadera')->default(true);
            $table->boolean('antena')->default(true);
            
            // LUCES
            $table->boolean('intermitentes')->default(true);
            $table->boolean('direccional_derecha')->default(true);
            $table->boolean('direccional_izquierda')->default(true);
            $table->boolean('luz_stop')->default(true);
            $table->boolean('faros')->default(true);
            $table->boolean('luces_altas')->default(true);
            $table->boolean('luz_interior')->default(true);
            $table->boolean('calaveras_buen_estado')->default(true);
            
            // OTROS (SEGURIDAD)
            $table->boolean('mata_chispas')->default(true);
            $table->boolean('alarma')->default(true);
            $table->boolean('extintor')->default(true);
            $table->boolean('botiquin')->default(true);
            $table->boolean('tarjeta_circulacion')->default(true);
            $table->boolean('licencia_conducir_vigente')->default(true);
            $table->boolean('poliza_seguro')->default(true);
            $table->boolean('triangulo_emergencia')->default(true);
            
            // INTERIOR
            $table->boolean('tablero_indicadores')->default(true);
            $table->boolean('switch_encendido')->default(true);
            $table->boolean('controles_ac')->default(true);
            $table->boolean('defroster')->default(true);
            $table->boolean('radio')->default(true);
            $table->boolean('volante')->default(true);
            $table->boolean('bolsas_aire')->default(true);
            $table->boolean('cinturon_seguridad')->default(true);
            $table->boolean('coderas')->default(true);
            $table->boolean('espejo_interior')->default(true);
            $table->boolean('freno_mano')->default(true);
            $table->boolean('encendedor')->default(true);
            $table->boolean('guantera')->default(true);
            $table->boolean('manijas_interiores')->default(true);
            $table->boolean('seguros')->default(true);
            $table->boolean('asientos')->default(true);
            $table->boolean('tapetes_delanteros_traseros')->default(true);
            
            // MOTOR
            $table->boolean('nivel_aceite_motor')->default(true);
            $table->boolean('nivel_anticongelante')->default(true);
            $table->boolean('nivel_liquido_frenos')->default(true);
            $table->boolean('bateria')->default(true);
            $table->boolean('bayoneta_aceite_motor')->default(true);
            $table->boolean('tapones')->default(true);
            $table->boolean('bocina_claxon')->default(true);
            $table->boolean('radiador')->default(true);
            
            // HERRAMIENTA
            $table->boolean('gato')->default(true);
            $table->boolean('llave_ruedas')->default(true);
            $table->boolean('cables_pasa_corriente')->default(true);
            $table->boolean('caja_bolsa_herramientas')->default(true);
            $table->boolean('dado_birlo_seguridad')->default(true);
            
            // CALCOMANIAS
            $table->boolean('calcomanias_permisos')->default(true);
            $table->boolean('calcomania_velocidad_maxima')->default(true);
            
            // OBSERVACIONES Y FIRMAS
            $table->text('mantenimiento_preventivo')->nullable();
            $table->text('mantenimiento_correctivo')->nullable();
            $table->json('condicion_carroceria_log')->nullable(); // Para el esquema gráfico de daños
            $table->string('responsable_recibo_uso')->nullable();
            $table->string('responsable_entrega')->nullable();
            
            $table->timestamps();

            $table->index(['ticket_id', 'tipo_inspeccion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
