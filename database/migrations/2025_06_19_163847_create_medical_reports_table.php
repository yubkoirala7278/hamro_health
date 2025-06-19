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

            $table->foreignId('student_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('school_id')
                ->constrained('schools', 'id')
                ->onDelete('cascade')
                ->name('medical_reports_school_id_foreign'); // ✅ explicit name

            $table->foreignId('uploaded_by')
                ->constrained('users', 'id')
                ->onDelete('restrict')
                ->name('medical_reports_uploaded_by_foreign'); // ✅ explicit name

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->date('checkup_date');
            $table->enum('report_type', ['checkup', 'external'])->default('checkup');
            $table->enum('status', ['pending', 'reviewed', 'approved'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
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
