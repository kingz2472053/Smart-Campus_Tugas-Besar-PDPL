<?php

namespace App\Services\Grading;

use App\Contracts\GradingStrategyInterface;

class PredicateGradingStrategy implements GradingStrategyInterface
{
    public function calculate(float $rawScore): string
    {
        // Menggunakan batas kelulusan >= 60
        return ($rawScore >= 60) ? 'Lulus' : 'Tidak Lulus';
    }
}