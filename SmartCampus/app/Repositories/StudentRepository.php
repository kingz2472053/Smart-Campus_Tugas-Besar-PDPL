<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function store(array $data)
    {
        return Student::create([
            'user_id' => $data['user_id'],
            'nim'     => $data['nim'],
            'major'   => $data['major'],
        ]);
    }
}