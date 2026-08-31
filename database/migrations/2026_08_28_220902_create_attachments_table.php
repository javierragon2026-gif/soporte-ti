<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            /*
             * Relación polimórfica.
             *
             * Puede pertenecer a un Comment, Note,
             * o cualquier otro modelo que utilice attachments().
             */
            $table->unsignedBigInteger('attachable_id');
            $table->string('attachable_type');

            $table->index([
                'attachable_type',
                'attachable_id',
            ]);

            /*
             * Usuario que subió el archivo.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Información del archivo.
             */
            $table->string('name');
            $table->string('path');

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
