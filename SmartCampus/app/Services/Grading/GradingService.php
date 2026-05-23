<?php

namespace App\Services\Grading;

use App\Contracts\GradingStrategyInterface;
use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;

class GradingService
{
    // Melakukan penilaian dan memicu State Pattern Calvin
    public function executeGrading(Submission $submission, float $rawScore, GradingStrategyInterface $strategy): void
    {
        $result = $strategy->calculate($rawScore);

        // 1. Simpan ke database
        \App\Models\Grade::create([
            'submission_id'    => $submission->id,
            'graded_by'        => \Illuminate\Support\Facades\Auth::id(),
            'grading_strategy' => (new \ReflectionClass($strategy))->getShortName(),
            'raw_score'        => $rawScore,
            'result'           => $result,
            'graded_at'        => now(),
        ]);

        // 2. Pemicu State Pattern Calvin
        // Menggunakan state yang diambil dari model
        $submission->getStateAttribute()->grade($submission);
    }
}