<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StationDataRequest;
use App\Models\Station;
use App\Models\SensorData;
use App\Services\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StationDataController extends Controller
{
    public function store(StationDataRequest $request): JsonResponse
    {
        $station = Station::where('serial_number', $request->serial)
            ->where('api_token', $request->token)
            ->firstOrFail();

        DB::transaction(function () use ($station, $request) {
            // Сохраняем данные
            $data = $station->sensorData()->create(
                array_merge($request->data, [
                    'timestamp' => $request->data['timestamp'] ?? now()
                ])
            );

            // Обновляем статус станции
            $station->update([
                'battery_level' => $request->data['battery'] ?? null,
                'signal_strength' => $request->data['signal'] ?? null,
                'gps_coordinates' => json_encode($request->data['gps']),
                'last_seen' => now(),
            ]);

            // Проверка перемещения и агро-параметров
            if ($station->expected_location && $station->gps_coordinates) {
                // NotificationService::triggerMovementAlert($station); // реализовать
            }
            if ($station->field && $station->field->currentCrop) {
                // NotificationService::checkSoilConditions($data, $station->field->currentCrop); // реализовать
            }
        });

        return response()->json(['status' => 'success']);
    }
}
