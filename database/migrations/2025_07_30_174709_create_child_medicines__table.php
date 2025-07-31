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
        Schema::create('child_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children_infos')->onDelete('cascade');
            $table->string('medicine_name'); // e.g., Amoxicillin
            $table->string('dosage'); // e.g., 250mg
            $table->string('frequency'); // e.g., 3 times daily, Every 8 hours
            $table->string('duration'); // e.g., 7 days
            $table->dateTime('next_dose_due')->nullable(); // e.g., 2025-07-30 16:00:00
            $table->string('status')->default('Pending'); // e.g., Given, Due Now, Pending
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_medicines_');
    }
};
