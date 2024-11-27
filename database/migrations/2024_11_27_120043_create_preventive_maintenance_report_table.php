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
        Schema::create('preventive_maintenance_report', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('preventive_id')->constrained('preventive_maintenance')->onDelete('cascade'); // Foreign key to preventive maintenance
            $table->foreignId('service_request_id')->nullable()->constrained('service_requests')->onDelete('cascade'); // Foreign key to service requests (nullable)
            $table->string('condition')->nullable(); // Equipment condition (e.g., 'Good', 'Damaged')
            $table->unsignedTinyInteger('health')->nullable(); // Health as a value from 1-10
            $table->string('other_info')->nullable(); // Additional information
            $table->timestamps(); // Created and Updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance_report');
    }
};
