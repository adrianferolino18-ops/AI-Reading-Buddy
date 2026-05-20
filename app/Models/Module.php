<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body_text'];

    /**
     * Get all saved vocabulary words tied to this reading text passage.
     */
    public function savedWords()
    {
        return $this->hasMany(SavedWord::class);
    }
}