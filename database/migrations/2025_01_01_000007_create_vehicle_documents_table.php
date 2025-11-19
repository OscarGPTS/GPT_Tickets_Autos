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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'tarjeta_circulacion',
                'poliza_seguro',
                'verificacion',
                'tenencia',
                'factura',
                'otro'
            ]);
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuer')->nullable(); // Aseguradora, autoridad, etc.
            $table->decimal('amount', 10, 2)->nullable(); // Monto (para seguros, tenencia)
            $table->enum('status', ['vigente', 'por_vencer', 'vencido'])->default('vigente');
            $table->string('document_path')->nullable(); // Ruta al archivo
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'document_type']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
