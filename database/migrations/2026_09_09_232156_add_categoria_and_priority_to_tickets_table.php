<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones: Agrega las columnas operativas al ticket.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Agregamos categoría y prioridad. Las ponemos como "nullable" 
            // para que los tickets viejos no choquen con este cambio.
            $table->string('categoria')->nullable()->after('status');
            $table->tinyInteger('priority')->default(2)->after('categoria');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['categoria', 'priority']);
        });
    }
};