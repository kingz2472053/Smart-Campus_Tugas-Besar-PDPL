<?php

namespace App\Services\Grading;

use App\Contracts\GradingStrategyInterface;

class NumericGradingStrategy implements GradingStrategyInterface
{
    public function calculate(float $rawScore): string
    {
        // Menyimpan nilai angka dengan format 2 digit desimal
        return number_format($rawScore, 2);
    }
}