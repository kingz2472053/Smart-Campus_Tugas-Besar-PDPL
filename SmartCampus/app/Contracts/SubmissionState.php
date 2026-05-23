<?php

namespace App\Contracts;

interface SubmissionState
{
    public function submit(\App\Models\Submission $submission): void;
    public function grade(\App\Models\Submission $submission): void;
    public function getStatus(): string;
}