<?php

namespace App\Services\Export;

use App\Contracts\ExportStrategyInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth; // 1. PASTIKAN INI SUDAH DIIMPOR

class CsvExportStrategy implements ExportStrategyInterface
{
    public function export(Collection $grades, string $courseName)
    {
        $fileName = 'Laporan_Nilai_' . str_replace(' ', '_', $courseName) . '.csv';

        return new StreamedResponse(function () use ($grades, $courseName) {
            $handle = fopen('php://output', 'w');

            // 2. Ambil data user dengan type-hint di dalam closure
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Header CSV
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Laporan Nilai', $courseName]);
            fputcsv($handle, ['Mahasiswa', $user->name]); // Gunakan variabel $user
            fputcsv($handle, []);
            
            fputcsv($handle, ['No', 'Judul Tugas', 'Waktu Kumpul', 'Nilai', 'Skor Maks']);

            $no = 1;
            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $no++,
                    $grade->submission->assignment->title ?? 'Tugas Dihapus',
                    $grade->created_at->format('Y-m-d H:i:s'),
                    $grade->result,
                    $grade->submission->assignment->max_score ?? '-'
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}