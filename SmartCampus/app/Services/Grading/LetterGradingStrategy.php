<?php

namespace App\Services\Grading;

use App\Contracts\GradingStrategyInterface;

class LetterGradingStrategy implements GradingStrategyInterface
{
    public function calculate(float $rawScore): string
    {
        if ($rawScore >= 80) return 'A';
        if ($rawScore >= 70) return 'B';
        if ($rawScore >= 60) return 'C';
        if ($rawScore >= 50) return 'D';
        return 'E';
    }
}