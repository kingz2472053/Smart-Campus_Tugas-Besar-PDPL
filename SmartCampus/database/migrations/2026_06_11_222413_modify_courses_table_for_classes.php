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
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->string('class_name')->default('A')->after('code');
            $table->string('academic_year')->default('2023/2024 Genap')->after('class_name');
            $table->unique(['code', 'class_name', 'academic_year'], 'courses_code_class_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique('courses_code_class_year_unique');
            $table->dropColumn('class_name');
            $table->dropColumn('academic_year');
            $table->unique('code');
        });
    }
};
