<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UiPreference extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'notification_channel',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
