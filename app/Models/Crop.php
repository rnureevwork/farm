<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $fillable = ['name', 'description'];
    
    public function cropRotations(): HasMany
    {
        return $this->hasMany(CropRotation::class);
    }
    
    public function referenceValues(): HasMany
    {
        return $this->hasMany(ReferenceData::class);
    }
}
