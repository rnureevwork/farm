<?php

namespace App\Services;

use App\Models\SensorData;
use App\Models\CropRotation;
use App\Models\Alert;

class AlertService
{
    public static function checkSoilConditions(SensorData $data, CropRotation $cropRotation)
    {
        $references = $cropRotation->crop->referenceValues;
        
        $soilMoistureRef = $references->firstWhere('parameter', 'soil_moisture_optimal');
        $tempRef = $references->firstWhere('parameter', 'temperature_frost_threshold');
        
        // Проверка влажности
        if ($soilMoistureRef && $data->soil_moisture < ($soilMoistureRef->value * 0.8)) {
            self::createAlert(
                $data->station,
                'soil_dry',
                "Критическая сухость почвы: {$data->soil_moisture}%"
            );
        }
        
        // Проверка заморозков
        if ($tempRef && $data->temperature < $tempRef->value) {
            self::createAlert(
                $data->station,
                'frost_risk',
                "Риск заморозков: {$data->temperature}°C"
            );
        }
    }

    public static function createAlert($station, $type, $message)
    {
        Alert::create([
            'station_id' => $station->id,
            'type' => $type,
            'message' => $message,
            'triggered_at' => now(),
        ]);
    }
} 