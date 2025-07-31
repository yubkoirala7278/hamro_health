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
        Schema::create('child_medical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children_infos')->onDelete('cascade');
            $table->string('file_path'); // Path to stored file (e.g., storage/app/medical_documents/...)
            $table->string('file_type'); // e.g., image/jpeg, application/pdf
            $table->string('file_name'); // Original file name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_medical_documents');
    }
};
