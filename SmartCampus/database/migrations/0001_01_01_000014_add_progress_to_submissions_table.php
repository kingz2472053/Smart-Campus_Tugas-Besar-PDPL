<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom 'progress' ke tabel 'submissions'
        // Schema::table('submissions', function (Blueprint $table) {
        //     $table->integer('progress')->default(0)->after('status');
        // });
    }

    public function down(): void
    {
        // Hapus kolom 'progress' jika rollback
        // Schema::table('submissions', function (Blueprint $table) {
        //     $table->dropColumn('progress');
        // });
    }
};
