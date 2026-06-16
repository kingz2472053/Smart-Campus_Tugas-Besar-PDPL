<?php

namespace App\Services\Export;

use App\Contracts\ExportStrategyInterface;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth; // 1. PASTIKAN INI SUDAH DIIMPOR

class PdfExportStrategy implements ExportStrategyInterface
{
    public function export(Collection $assignments, string $courseName)
    {
        $fileName = 'Laporan_Nilai_' . str_replace(' ', '_', $courseName) . '.pdf';

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pdf = Pdf::loadView('exports.grades_pdf', [
            'assignments' => $assignments, // Ubah dari 'grades' ke 'assignments'
            'courseName'  => $courseName,
            'studentName' => $user->name,
            'nim'         => $user->student->nim ?? '-'
        ]);

        return $pdf->download($fileName);
    }
}