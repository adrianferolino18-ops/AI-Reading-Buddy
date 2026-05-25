<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedWord extends Model
{
    // Allows fields to pass through safely without blocking database updates
    protected $fillable = [
        'user_id',
        'module_id',
        'word',
        'definition',
        'context',
        'hint_1',
        'hint_2',
        'hint_3'
    ];

    /**
     * Relationship link back to parental workspace module maps.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}