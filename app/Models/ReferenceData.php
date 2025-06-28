<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceData extends Model
{
    protected $fillable = ['crop_id', 'parameter', 'value'];
    
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
