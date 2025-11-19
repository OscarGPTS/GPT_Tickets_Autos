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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique()->nullable(); // AAAA-NNNN (generado al aprobar)
            
            // Relaciones
            $table->foreignId('user_id')->constrained()->comment('Solicitante');
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->foreignId('dispatcher_id')->nullable()->constrained('users')->comment('Despachador asignado');
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('Encargado que aprobó');
            
            // Datos de la requisición
            $table->string('destination');
            $table->text('purpose'); // Motivo del viaje
            $table->date('requested_date');
            $table->time('requested_time_start');
            $table->time('requested_time_end')->nullable();
            $table->integer('passenger_count')->default(1);
            $table->text('additional_notes')->nullable();
            
            // Datos del conductor (si es diferente al solicitante)
            $table->string('conductor_name')->nullable();
            $table->string('conductor_phone')->nullable();
            $table->foreignId('conductor_license_id')->nullable()->constrained('driver_licenses');
            
            // Estados del ticket
            $table->enum('status', [
                'pendiente',              // Recién creado
                'aprobado',               // Aprobado por encargado, asignado
                'rechazado',              // Rechazado por encargado
                'en_curso',               // Checkout realizado
                'finalizado',             // Checkin realizado
                'completado'              // Calificado por usuario
            ])->default('pendiente');
            
            // Fechas de workflow
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('checkout_at')->nullable();
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Motivo de rechazo
            $table->text('rejection_reason')->nullable();
            
            // Calificación del servicio
            $table->tinyInteger('service_rating')->nullable()->comment('1-5 estrellas');
            $table->tinyInteger('vehicle_rating')->nullable()->comment('1-5 estrellas');
            $table->text('rating_comments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Índices para mejorar performance
            $table->index('folio');
            $table->index('status');
            $table->index('user_id');
            $table->index('dispatcher_id');
            $table->index('requested_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
