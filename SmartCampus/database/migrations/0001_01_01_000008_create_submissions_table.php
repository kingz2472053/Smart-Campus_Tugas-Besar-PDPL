<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_format')->nullable();
            $table->integer('file_size_kb')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'late'])->default('draft');
            $table->enum('progress', ['not_started', 'on_progress', 'completed'])->default('not_started');
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
