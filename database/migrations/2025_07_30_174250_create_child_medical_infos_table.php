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
        Schema::create('child_medical_infos', function (Blueprint $table) {
            $table->id();
            // Foreign key to children_infos
            $table->foreignId('child_id')
                ->constrained('children_infos')
                ->cascadeOnDelete();

            $table->string('blood_group')->nullable(); // e.g., O Positive
            $table->text('allergies')->nullable(); // e.g., Peanuts, Shellfish
            $table->text('current_vitamins')->nullable(); // e.g., Multivitamins
            $table->text('recent_illness')->nullable(); // e.g., Fever
            $table->date('last_updated_date')->nullable(); // e.g., 2025-06-11
            $table->string('current_status')->nullable(); // e.g., Vaccination up to date
            $table->date('annual_checkup')->nullable(); // e.g., 2025-12-15
            $table->text('immunizations')->nullable(); // e.g., Covid 19, Flu Shot 2024, MMR, Tdap
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_medical_infos');
    }
};
