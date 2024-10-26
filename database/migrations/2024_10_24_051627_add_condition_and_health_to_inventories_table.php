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
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('status')->default('Available')->after('department'); // Add 'condition' column with a default value
            $table->string('condition')->default('Good')->after('status'); // Add 'condition'(e.g., Good, Needs Repair, Critical)
            $table->integer('health')->default(100)->after('condition');   // Add 'health' 0-100 column with a default value of 100
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('status'); // Remove 'condition' column if rolling back
            $table->dropColumn('condition'); // Remove 'condition' column if rolling back
            $table->dropColumn('health');    // Remove 'health' column if rolling back
        });
    }
};
