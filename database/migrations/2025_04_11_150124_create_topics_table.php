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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            // Liên kết với user (leader) đăng ký ý tưởng
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('topic_name')->comment('Tên ý tưởng');
            $table->string('leader_email')->comment('Email của người đăng ký (leader)');
            $table->text('description')->nullable()->comment('Mô tả ý tưởng');
            // Trạng thái của topic: pending, approved, rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->comment('Trạng thái duyệt ý tưởng');
            $table->enum('award', ['first', 'second', 'third'])
                  ->nullable()
                  ->comment('Giải thưởng được trao');
            $table->unsignedSmallInteger('submission_year')->nullable();
            $table->string('report_file')->nullable();
            $table->string('topic_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
