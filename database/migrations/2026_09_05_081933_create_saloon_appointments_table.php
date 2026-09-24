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
        Schema::create('saloon_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('saloon_services')->onDelete('restrict');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['booked', 'completed', 'cancelled'])->default('booked');
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            $table->decimal('amount', 8, 2);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saloon_appointments');
    }
};
