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
        throw new Exception("Tugas ini sudah selesai dinilai.");
    }

    public function getStatus(): string
    {
        return 'graded';
    }
}
