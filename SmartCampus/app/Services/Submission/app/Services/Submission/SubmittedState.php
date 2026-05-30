<?php

namespace App\Services\Submission;

use App\Contracts\SubmissionState;
use App\Models\Submission;
use Exception;

class SubmittedState implements SubmissionState
{
    public function submit(Submission $submission): void
    {
        throw new Exception("Tugas sudah dikumpulkan sebelumnya.");
    }

    public function grade(Submission $submission): void
    {
        $submission->status = 'graded';
        $submission->progress = 100; // Progres penuh 100% setelah dinilai dosen
        $submission->save();
    }

    public function getStatus(): string
    {
        return 'submitted';
    }
}
