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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('registration_number')->unique();
            $table->string('designation');
            $table->string('higher_degree')->nullable();
            $table->foreignId('subcategory_id')->constrained('subcategories')->onDelete('restrict');
            $table->string('image')->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->integer('experience_years')->default(0);
            $table->decimal('fee', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->integer('avg_delay_minutes')->default(0);
            $table->integer('additional_time_minutes')->default(0);
            $table->string('available_days'); // "Mon,Wed,Fri"
            $table->time('starting_time');
            $table->time('ending_time');
            $table->time('break_time_start')->nullable();
            $table->time('break_time_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
