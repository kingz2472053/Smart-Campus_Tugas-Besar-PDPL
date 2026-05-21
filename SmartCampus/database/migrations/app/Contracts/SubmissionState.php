<?php

namespace App\Contracts;

use App\Models\Submission;

interface SubmissionState
{
    public function submit(Submission $submission): void;
    public function grade(Submission $submission): void;
    public function getStatus(): string;
}
