<?php

namespace App\Services\Submission;

use App\Contracts\SubmissionState;
use App\Models\Submission;
use Exception;

class SubmittedState implements SubmissionState
{
    public function submit(Submission $submission): void
    {
        throw new Exception("Tugas sudah dikumpulkan.");
    }

    public function grade(Submission $submission): void
    {
        // Transisi ke status graded (logika dari Calvin)
        $submission->update(['status' => 'graded', 'progress' => 100]);
    }

    public function getStatus(): string
    {
        return 'submitted';
    }
}