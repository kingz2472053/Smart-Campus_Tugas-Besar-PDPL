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
        
        // 2. Gunakan Facade Auth dan tambahkan type-hint untuk IDE
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $studentId = $user->student->id ?? null;

        if (!$studentId) {
            abort(403, 'Anda bukan mahasiswa.');
        }

        // Ambil data nilai khusus untuk mahasiswa ini, pada course ini
        $grades = Grade::whereHas('submission', function($query) use ($course, $studentId) {
            $query->where('student_id', $studentId)
                  ->whereHas('assignment', function($q) use ($course) {
                      $q->where('course_id', $course->id);
                  });
        })->get();

        if ($grades->isEmpty()) {
            return back()->with('error', 'Tidak ada data nilai untuk diekspor.');
        }

        $strategy = match ($format) {
            'csv' => new CsvExportStrategy(),
            default => new PdfExportStrategy(),
        };

        return $strategy->export($grades, $course->name);
    }
}