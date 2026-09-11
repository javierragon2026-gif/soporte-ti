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
        Schema::create('home_office_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            
            $table->string('request_type')->default('shared_loan'); // shared_loan, assigned_gate_pass
            
            $table->date('scheduled_start_date');
            $table->date('scheduled_end_date');
            
            $table->timestamp('checkout_at')->nullable();
            $table->timestamp('checkin_at')->nullable();
            
            $table->foreignId('checkout_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checkin_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('status')->default('pending'); // pending, approved, active, returned, cancelled
            
            $table->json('accessories')->nullable(); // Guardar qué accesorios se prestaron
            $table->text('checkout_notes')->nullable();
            $table->text('checkin_notes')->nullable();
            
            $table->boolean('policy_accepted')->default(false); // Responsiva digital
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_office_requests');
    }
};
