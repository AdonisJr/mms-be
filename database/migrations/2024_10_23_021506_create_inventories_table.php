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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_type');     // Type of equipment
            $table->string('model')->nullable();  // Model of the equipment
            $table->date('acquisition_date');     // Date of acquisition
            $table->string('location');           // Current location of the equipment
            $table->string('warranty')->nullable(); // Warranty information
            $table->string('department');         // Department the equipment is assigned to
            $table->timestamps();                 // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
