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
        Schema::create('preventive_maintenance_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preventive_maintenance_id'); // Foreign key for the preventive maintenance
            $table->unsignedBigInteger('user_id'); // Foreign key for the user
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('preventive_maintenance_id')->references('id')->on('preventive_maintenance')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance_user');
    }
};
