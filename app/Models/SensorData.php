<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorData extends Model
{
    protected $fillable = [
        'station_id', 'timestamp', 'temperature', 'humidity', 'soil_moisture',
        'precipitation', 'wind_speed', 'wind_direction', 'pressure'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'temperature' => 'float',
        'humidity' => 'integer',
        'soil_moisture' => 'integer',
        'precipitation' => 'float',
        'wind_speed' => 'float',
        'wind_direction' => 'integer',
        'pressure' => 'float',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
