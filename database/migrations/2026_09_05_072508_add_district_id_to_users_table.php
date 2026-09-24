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
            //
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('state')->nullable();
            $table->string('village_name')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropForeignIdFor('District');
            $table->dropColumn(['district_id','gender', 'age', 'state', 'village_name', 'height', 'weight', 'avatar', 'phone', 'is_active', 'deleted_at']);
        });
    }
};
