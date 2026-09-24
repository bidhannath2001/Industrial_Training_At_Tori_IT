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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['doctor', 'beautician']);
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('location');
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->string('state');
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->decimal('gst_amount', 8, 2)->nullable();
            $table->decimal('base_amount', 8, 2)->nullable();
            $table->decimal('commission', 5, 2)->nullable();
            $table->decimal('zapmor_commission', 5, 2)->nullable();
            $table->decimal('booking_fee', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
