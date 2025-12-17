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
        Schema::create('vehicle_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('maintenance_type', ['preventivo', 'correctivo', 'emergencia']);
            $table->date('maintenance_date');
            $table->decimal('mileage_at_maintenance', 20, 2);
            $table->string('service_provider'); // Taller/Proveedor
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('next_maintenance_date')->nullable();
            $table->decimal('next_maintenance_mileage', 20, 2)->nullable();
            $table->text('parts_replaced')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_path')->nullable();
            $table->enum('status', ['programado', 'en_proceso', 'completado', 'cancelado'])->default('programado');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'maintenance_date']);
            $table->index('next_maintenance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance');
    }
};
