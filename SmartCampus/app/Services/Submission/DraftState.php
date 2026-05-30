<?php

namespace App\Services\Submission;

use App\Contracts\SubmissionState;
use App\Models\Submission;
use Exception;

class DraftState implements SubmissionState
{
    public function submit(Submission $submission): void
    {
        $submission->status = 'submitted';
        $submission->progress = 50; // Progres otomatis 50% di dashboard saat dikumpul
        $submission->save();
    }

    public function grade(Submission $submission): void
    {
        throw new Exception("Tugas berstatus Draft tidak bisa langsung dinilai.");
    }

    public function getStatus(): string
    {
        return 'draft';
    }
}
