<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Grade;
use App\Services\Export\PdfExportStrategy;
use App\Services\Export\CsvExportStrategy;
use Illuminate\Support\Facades\Auth; // 1. PASTIKAN INI SUDAH DIIMPOR

class ExportController extends Controller
{
    public function exportCourseGrades(Request $request, Course $course)
    {
        $format = $request->query('format', 'pdf');
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $studentId = $user->student->id ?? null;

        if (!$studentId) {
            abort(403, 'Anda bukan mahasiswa.');
        }

        // UBAH KUERI: Ambil dari Assignment, bukan Grade
        $assignments = $course->assignments()
            ->with(['submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId)->with('latestGrade');
            }])
            ->orderBy('deadline', 'desc')
            ->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'Tidak ada tugas untuk diekspor.');
        }

        $strategy = match ($format) {
            'csv' => new CsvExportStrategy(),
            default => new PdfExportStrategy(),
        };

        // Kirim data $assignments, bukan $grades
        return $strategy->export($assignments, $course->name);
    }
}