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
        Schema::create('school_user', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('user_id');

            // Foreign Keys with explicit names
            $table->foreign('school_id', 'school_user_school_id_foreign')
                ->references('id')->on('schools')
                ->onDelete('cascade');

            $table->foreign('user_id', 'school_user_user_id_foreign')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->primary(['school_id', 'user_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_user');
    }
};
