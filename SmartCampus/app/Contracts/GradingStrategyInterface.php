<?php

namespace App\Contracts;

/**
 * GradingStrategyInterface — Strategy Pattern
 * Diletakkan di app/Contracts agar konsisten dengan interface lainnya
 * seperti AuthServiceInterface dan TaskCommandInterface.
 */
interface GradingStrategyInterface
{
    public function calculate(float $rawScore): string;
}