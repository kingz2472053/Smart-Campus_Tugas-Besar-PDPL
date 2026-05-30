<?php

namespace App\Services\Submission;

use App\Contracts\SubmissionState;
use App\Models\Submission;
use Exception;

class GradedState implements SubmissionState
{
    public function submit(Submission $submission): void
    {
        throw new Exception("Tugas yang sudah dinilai tidak bisa dikumpulkan ulang.");
    }

    public function grade(Submission $submission): void
    {
        // Tugas sudah dinilai, tapi kita izinkan update/re-grade tanpa mengubah state.
        // Tidak perlu throw exception di sini.
        $submission->update(['status' => 'graded']); // Pastikan tetap graded
    }

    public function getStatus(): string
    {
        return 'graded';
    }
}
