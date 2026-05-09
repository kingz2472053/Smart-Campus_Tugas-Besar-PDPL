<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            $table->foreignId('graded_by')->constrained('users')->onDelete('cascade');
            $table->enum('grading_strategy', ['numeric', 'letter', 'predicate'])->default('numeric');
            $table->decimal('raw_score', 5, 2);
            $table->string('result');
            $table->timestamp('graded_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
