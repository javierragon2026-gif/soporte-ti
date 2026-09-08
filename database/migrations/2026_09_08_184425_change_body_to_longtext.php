<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta la migración: Amplía la capacidad de las columnas body.
     */
    public function up(): void
    {
        // Ampliamos el límite en ambas tablas para soportar imágenes pegadas en Base64
        DB::statement("ALTER TABLE tickets MODIFY body LONGTEXT");
        DB::statement("ALTER TABLE comments MODIFY body LONGTEXT");
    }

    /**
     * Revierte los cambios si es necesario.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY body TEXT");
        DB::statement("ALTER TABLE comments MODIFY body TEXT");
    }
};