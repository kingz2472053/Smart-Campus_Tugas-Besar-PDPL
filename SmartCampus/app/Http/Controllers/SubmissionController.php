<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * SubmissionController — Mengelola pengumpulan tugas oleh mahasiswa.
 *
 * Controller ini menangani upload file submission dari mahasiswa.
 * Validasi file dilakukan berdasarkan setting assignment:
 * - Format file harus sesuai (file_format_allowed)
 * - Ukuran file tidak boleh melebihi batas (max_file_size_kb)
 * - Deadline dicek untuk menentukan status (submitted/late)
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Hanya menangani submission
 * - Guard Clauses: Validasi akses dan deadline di awal
 * - Meaningful Names: Variabel dan method deskriptif
 */
class SubmissionController extends Controller
{
    /**
     * Menyimpan submission baru atau mengupdate yang sudah ada.
     *
     * Alur:
     * 1. Validasi akses mahasiswa (enrollment check)
     * 2. Validasi file (format + ukuran sesuai setting assignment)
     * 3. Simpan file ke storage
     * 4. Buat/update record submission
     * 5. Log aktivitas via ActivityLogger (Singleton)
     */
    public function store(Request $request, Assignment $assignment)
    {
        $user = Auth::user();

        // Guard clause: Pastikan user adalah mahasiswa dengan profil lengkap
        if (!$user->student) {
            abort(403, 'Profil mahasiswa tidak ditemukan.');
        }

        // Guard clause: Pastikan mahasiswa terdaftar di course ini
        $this->authorizeEnrollment($user, $assignment);

        // Validasi file upload
        $allowedFormats = $this->parseAllowedFormats($assignment->file_format_allowed);
        $maxSizeKb = $assignment->max_file_size_kb;

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedFormats),
                'max:' . $maxSizeKb,
            ],
        ], [
            'file.required' => 'File tugas wajib diupload.',
            'file.mimes'    => 'Format file harus: ' . $assignment->file_format_allowed,
            'file.max'      => 'Ukuran file maksimal ' . $this->formatFileSize($maxSizeKb) . '.',
        ]);

        // Simpan file ke storage
        $file = $request->file('file');
        $fileName = $this->generateFileName($user->student->id, $assignment->id, $file);
        $filePath = $file->storeAs('submissions', $fileName, 'public');

        // Tentukan status berdasarkan deadline
        $status = $this->determineSubmissionStatus($assignment);

        // Buat atau update submission (upsert berdasarkan student + assignment)
        $submission = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id'    => $user->student->id,
            ],
            [
                'file_path'    => $filePath,
                'file_name'    => $file->getClientOriginalName(),
                'file_format'  => $file->getClientOriginalExtension(),
                'file_size_kb' => (int) ceil($file->getSize() / 1024),
                'submitted_at' => now(),
                'status'       => $status,
                'progress'     => 'on_progress',
            ]
        );

        // Log aktivitas via Singleton Pattern
        ActivityLogger::getInstance()->log(
            action: 'SUBMIT_ASSIGNMENT',
            userId: $user->id,
            targetTable: 'submissions',
            targetId: $submission->id,
            detail: [
                'assignment_title' => $assignment->title,
                'file_name'        => $file->getClientOriginalName(),
                'status'           => $status,
            ]
        );

        $routeName = 'mahasiswa.assignments.show';

        return redirect()
            ->route($routeName, $assignment)
            ->with('success', 'Tugas berhasil dikumpulkan!');
    }

    /**
     * Mengupdate submission yang sudah ada (re-upload).
     * Hanya bisa dilakukan jika submission belum dinilai.
     */
    public function update(Request $request, Submission $submission)
    {
        $user = Auth::user();

        // Guard clause: Pastikan submission milik mahasiswa ini
        if (!$user->student || $submission->student_id !== $user->student->id) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }

        $assignment = $submission->assignment;

        // Validasi file upload
        $allowedFormats = $this->parseAllowedFormats($assignment->file_format_allowed);
        $maxSizeKb = $assignment->max_file_size_kb;

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedFormats),
                'max:' . $maxSizeKb,
            ],
        ], [
            'file.required' => 'File tugas wajib diupload.',
            'file.mimes'    => 'Format file harus: ' . $assignment->file_format_allowed,
            'file.max'      => 'Ukuran file maksimal ' . $this->formatFileSize($maxSizeKb) . '.',
        ]);

        // Hapus file lama jika ada
        if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        // Simpan file baru
        $file = $request->file('file');
        $fileName = $this->generateFileName($user->student->id, $assignment->id, $file);
        $filePath = $file->storeAs('submissions', $fileName, 'public');

        // Update submission
        $status = $this->determineSubmissionStatus($assignment);
        $submission->update([
            'file_path'    => $filePath,
            'file_name'    => $file->getClientOriginalName(),
            'file_format'  => $file->getClientOriginalExtension(),
            'file_size_kb' => (int) ceil($file->getSize() / 1024),
            'submitted_at' => now(),
            'status'       => $status,
        ]);

        // Log aktivitas
        ActivityLogger::getInstance()->log(
            action: 'RESUBMIT_ASSIGNMENT',
            userId: $user->id,
            targetTable: 'submissions',
            targetId: $submission->id,
            detail: [
                'assignment_title' => $assignment->title,
                'file_name'        => $file->getClientOriginalName(),
                'status'           => $status,
            ]
        );

        return redirect()
            ->route('mahasiswa.assignments.show', $assignment)
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    // ──────────────────────────────────────
    // Private Helper Methods
    // ──────────────────────────────────────

    /**
     * Verifikasi bahwa mahasiswa terdaftar aktif di course assignment.
     */
    private function authorizeEnrollment($user, Assignment $assignment): void
    {
        $isEnrolled = $user->student->enrollments()
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di mata kuliah ini.');
        }
    }

    /**
     * Parse string format file menjadi array.
     * Contoh: 'pdf,doc,docx,zip' → ['pdf', 'doc', 'docx', 'zip']
     */
    private function parseAllowedFormats(string $formats): array
    {
        return array_map('trim', explode(',', $formats));
    }

    /**
     * Generate nama file unik untuk submission.
     * Format: {studentId}_{assignmentId}_{timestamp}.{ext}
     */
    private function generateFileName(int $studentId, int $assignmentId, $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');

        return "{$studentId}_{$assignmentId}_{$timestamp}.{$extension}";
    }

    /**
     * Tentukan status submission berdasarkan deadline assignment.
     * - 'submitted': jika masih dalam deadline
     * - 'late': jika sudah melewati deadline
     */
    private function determineSubmissionStatus(Assignment $assignment): string
    {
        return now()->isAfter($assignment->deadline) ? 'late' : 'submitted';
    }

    /**
     * Format ukuran file dari KB ke format yang mudah dibaca.
     * Contoh: 10240 → '10 MB'
     */
    private function formatFileSize(int $sizeKb): string
    {
        if ($sizeKb >= 1024) {
            return round($sizeKb / 1024, 1) . ' MB';
        }

        return $sizeKb . ' KB';
    }
}
