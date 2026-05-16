<?php

namespace App\Models;

use App\Contracts\SubjectInterface;
use App\Contracts\ObserverInterface; // Tambahan import
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model implements SubjectInterface
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'deadline',
        'max_score',
        'file_format_allowed',
        'max_file_size_kb',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    // ==========================================
    // IMPLEMENTASI OBSERVER PATTERN (SUBJECT)
    // ==========================================
    private array $observers = [];

    public function attach(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(ObserverInterface $observer): void
    {
        $this->observers = array_filter($this->observers, function ($obs) use ($observer) {
            return $obs !== $observer;
        });
    }

    public function notifyObservers(array $targetStudentIds): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this, $targetStudentIds);
        }
    }
    // ==========================================

    // Relasi Eloquent
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}