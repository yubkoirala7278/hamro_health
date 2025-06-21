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
        Schema::create('medical_reports', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade')
                ->comment('Links to the student record');

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('School admin who created the report');

            // Basic report info
            $table->date('report_date')->comment('Date of the medical report');
            $table->string('medical_condition', 255)->nullable()->comment('Medical condition or diagnosis');
            $table->text('allergies')->nullable()->comment('Allergies, if any');
            $table->text('medications')->nullable()->comment('Current medications');
            $table->text('vaccinations')->nullable()->comment('Vaccination history');
            $table->text('notes')->nullable()->comment('Additional notes or observations');

            // Doctor information
            $table->string('doctor_name', 255)->nullable()->comment('Name of the doctor or medical professional');
            $table->string('doctor_contact', 20)->nullable()->comment('Doctor’s contact information');
            $table->string('specialist', 100)->nullable()->comment('Specialist field of the doctor');
            $table->string('mnc_number', 100)->nullable()->comment('Medical Council registration number (MNC)');

            // Vitals and detailed information
            $table->string('blood_pressure', 20)->nullable()->comment('Blood pressure, e.g. 120/80');
            $table->string('pulse_rate', 20)->nullable()->comment('Pulse rate in bpm');
            $table->string('temperature', 10)->nullable()->comment('Body temperature');
            $table->string('respiratory_rate', 10)->nullable()->comment('Respiratory rate in bpm');
            $table->string('oxygen_saturation', 10)->nullable()->comment('Oxygen saturation level (%)');

            // Report file
            $table->string('report_file')->nullable()->comment('Path to the uploaded medical report file');

            // Status & control
            $table->string('status', 20)->default('active')->comment('Report status: active, archived');
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete for record recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_reports');
    }
};
