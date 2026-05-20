<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedWord extends Model
{
    // Allows fields to pass through safely without blocking database updates
    protected $fillable = [
        'word', 
        'definition', 
        'module_id', 
        'context'
    ];

    /**
     * Relationship link back to parental workspace module maps.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}