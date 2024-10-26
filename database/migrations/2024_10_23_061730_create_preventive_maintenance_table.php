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
        Schema::create('preventive_maintenance', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the preventive maintenance task
            $table->string('description'); // Description of the preventive maintenance task
            $table->date('scheduled_date_from'); // Start date for the maintenance
            $table->date('scheduled_date_to'); // End date for the maintenance
            $table->string('status'); // Status of the maintenance task (e.g., pending, in-progress, completed)
            $table->unsignedBigInteger('created_by'); // ID of the user who created the maintenance task
            $table->timestamps();
            
            // Foreign key for the user who created the task
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance');
    }
};
