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
        Schema::create('topic_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')
                  ->constrained('topics')
                  ->onDelete('cascade');
            $table->string('name');
            $table->string('student_id');
            $table->foreignId('university_id')
                  ->constrained('universities')
                  ->onDelete('cascade')
                  ->comment('Trường của thành viên');
            $table->foreignId('faculty_id')
                  ->constrained('faculties')
                  ->onDelete('cascade')
                  ->comment('Khoa của thành viên');
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_members');
    }
};
