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
        Schema::create('clinic_visits', function (Blueprint $table) {
             $table->id();
            $table->foreignId('child_id')->constrained('children_infos')->onDelete('cascade');
            $table->date('visit_date');
            $table->string('reason'); // e.g., Routine Checkup, Fever
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_visits');
    }
};
