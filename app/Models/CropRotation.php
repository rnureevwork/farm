<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CropRotation extends Model
{
    protected $fillable = ['field_id', 'crop_id', 'planting_date', 'harvest_date'];
    
    protected $casts = [
        'planting_date' => 'date',
        'harvest_date' => 'date',
    ];
    
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
    
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
    
    public function referenceValues(): HasManyThrough
    {
        return $this->hasManyThrough(
            ReferenceData::class, 
            Crop::class,
            'id', 
            'crop_id',
            'crop_id',
            'id'
        );
    }
}
