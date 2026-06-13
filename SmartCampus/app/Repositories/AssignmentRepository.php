<?php

namespace App\Repositories;

use App\Models\Assignment;
use App\Repositories\Contracts\AssignmentRepositoryInterface;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    public function store(array $data)
    {
        return Assignment::create([
            'course_id'   => $data['course_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'deadline'    => $data['deadline'],
            'created_by'  => $data['created_by'], 
        ]);
    }
}