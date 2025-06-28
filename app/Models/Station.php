<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $fillable = [
        'serial_number', 'battery_level', 'signal_strength', 'movement_speed',
        'gps_coordinates', 'expected_location', 'last_seen', 'is_active', 'api_token', 'field_id'
    ];

    protected $casts = [
        'gps_coordinates' => 'array', // Для PostGIS Point
        'expected_location' => 'array',
        'last_seen' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class)->withDefault();
    }

    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class)->orderBy('timestamp', 'DESC');
    }

    public function latestData(): HasMany
    {
        return $this->hasOne(SensorData::class)->latest('timestamp');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
