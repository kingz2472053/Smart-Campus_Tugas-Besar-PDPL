<?php

namespace App\Repositories;

use App\Models\Grade;
use App\Repositories\Contracts\GradeRepositoryInterface;

class GradeRepository implements GradeRepositoryInterface
{
    public function store(array $data)
    {
        return Grade::create([
            'student_id'    => $data['student_id'],
            'course_id'     => $data['course_id'],
            'score'         => $data['score'],
        ]);
    }
}