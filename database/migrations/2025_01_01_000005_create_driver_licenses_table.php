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
        Schema::create('driver_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->string('license_type'); // A, B, C, D, E
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('status', ['vigente', 'vencida', 'suspendida'])->default('vigente');
            $table->string('issuing_authority')->nullable();
            $table->text('restrictions')->nullable();
            $table->string('document_path')->nullable(); // Ruta al archivo escaneado
            $table->timestamps();
            $table->softDeletes();

            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_licenses');
    }
};
