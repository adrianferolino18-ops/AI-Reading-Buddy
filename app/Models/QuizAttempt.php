<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = ['user_id', 'saved_word_id', 'wrong_attempts_count', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedWord(): BelongsTo
    {
        return $this->belongsTo(SavedWord::class);
    }
}