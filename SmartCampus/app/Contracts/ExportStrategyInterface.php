<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ExportStrategyInterface
{
    /**
     * Mengekspor data grades menjadi file (PDF atau CSV).
     *
     * @param Collection $grades Data nilai yang akan diekspor.
     * @param string $courseName Nama mata kuliah untuk nama file.
     * @return mixed Response file yang akan didownload.
     */
    public function export(Collection $grades, string $courseName);
}