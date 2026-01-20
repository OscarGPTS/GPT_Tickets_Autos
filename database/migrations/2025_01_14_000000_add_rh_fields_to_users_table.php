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
        Schema::table('users', function (Blueprint $table) {
            // Agregar campos si no existen
            if (!Schema::hasColumn('users', 'rh_user_id')) {
                $table->integer('rh_user_id')->nullable()->unique()->after('auth0_id');
            }
            if (!Schema::hasColumn('users', 'rh_uuid')) {
                $table->string('rh_uuid')->nullable()->unique()->after('rh_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rh_user_id', 'rh_uuid']);
        });
    }
};
