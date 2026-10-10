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
        Schema::table('tickets', function (Blueprint $table) {
            $table->text('email_to')->nullable()->after('body');
            $table->text('email_cc')->nullable()->after('email_to');
            $table->string('message_id', 255)->nullable()->after('email_cc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['email_to', 'email_cc', 'message_id']);
        });
    }
};
