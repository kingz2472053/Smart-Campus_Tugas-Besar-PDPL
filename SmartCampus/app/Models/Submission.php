<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'file_name',
        'file_format',
        'file_size_kb',
        'submitted_at',
        'status',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the latest grade for this submission.
     */
    public function latestGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class)->latestOfMany();
    }
    
    /**
     * Membaca status string dari DB ke bentuk objek State Pattern
     */
    public function getStateAttribute()
    {
        return match ($this->status) {
            'submitted' => new \App\Services\Submission\SubmittedState(),
            'graded' => new \App\Services\Submission\GradedState(),
            default => new \App\Services\Submission\DraftState(),
        };
    }

    public function submitTask(): void
    {
        $this->state->submit($this);
    }

    public function gradeTask(): void
    {
        $this->state->grade($this);
    }

