<?php

namespace App\Services\Export;

use App\Contracts\ExportStrategyInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth; // 1. PASTIKAN INI SUDAH DIIMPOR

class CsvExportStrategy implements ExportStrategyInterface
{
    public function export(Collection $assignments, string $courseName)
    {
        $fileName = 'Laporan_Nilai_' . str_replace(' ', '_', $courseName) . '.csv';

        return new StreamedResponse(function () use ($assignments, $courseName) {
            $handle = fopen('php://output', 'w');

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Header CSV
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['Laporan Nilai', $courseName]);
            fputcsv($handle, ['Mahasiswa', $user->name]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['No', 'Judul Tugas', 'Waktu Kumpul', 'Nilai', 'Skor Maks']);

            $no = 1;
            // Looping melalui $assignments
            foreach ($assignments as $assignment) {
                $sub = $assignment->submissions->first();
                fputcsv($handle, [
                    $no++,
                    $assignment->title,
                    $sub && $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i:s') : 'Belum Kumpul',
                    $sub && $sub->latestGrade ? $sub->latestGrade->result : '-',
                    $assignment->max_score
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}