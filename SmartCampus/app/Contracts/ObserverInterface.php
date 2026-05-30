<?php

namespace App\Contracts;

use App\Models\Assignment;

interface ObserverInterface
{
    // Method ini akan dipanggil oleh Subject
    public function update(Assignment $assignment, array $targetStudentIds): void;
}