<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Field extends Model
{
    protected $fillable = ['name', 'area_hectares', 'farm_id', 'boundary'];
    
    protected $casts = [
        'boundary' => 'array', // Для PostgreSQL geometry
    ];
    
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
    
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }
    
    public function cropRotations(): HasMany
    {
        return $this->hasMany(CropRotation::class);
    }
    
    public function currentCrop(): HasOne
    {
        return $this->hasOne(CropRotation::class)
            ->whereDate('planting_date', '<=', now())
            ->whereDate('harvest_date', '>=', now())
            ->latest();
    }
    
    public function latestData()
    {
        return $this->hasOneThrough(
            SensorData::class,
            Station::class,
            'field_id',
            'station_id',
            'id',
            'id'
        )->latest('sensor_data.timestamp');
    }
}
