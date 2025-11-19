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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code')->unique(); // Código interno de GPT
            $table->string('brand'); // Marca
            $table->string('model'); // Modelo
            $table->year('year');
            $table->string('plates')->unique(); // Placas
            $table->string('serial_number')->unique()->nullable(); // Número de serie/VIN
            $table->string('color')->nullable();
            $table->enum('vehicle_type', ['sedan', 'suv', 'pickup', 'van', 'camioneta'])->default('sedan');
            $table->integer('capacity_passengers')->default(5);
            $table->decimal('capacity_cargo', 10, 2)->nullable(); // Capacidad de carga en kg
            $table->enum('fuel_type', ['gasolina', 'diesel', 'electrico', 'hibrido'])->default('gasolina');
            $table->decimal('current_mileage', 20, 2)->default(0);
            $table->enum('status', ['disponible', 'en_uso', 'mantenimiento', 'fuera_servicio'])->default('disponible');
            $table->text('notes')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
