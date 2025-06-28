<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(): JsonResponse
    {
        $stations = Station::with(['field.farm'])->get();
        return response()->json($stations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => 'required|string|unique:stations',
            'field_id' => 'required|exists:fields,id',
            'expected_location' => 'nullable|array',
            'api_token' => 'required|string|unique:stations'
        ]);

        $station = Station::create($validated);
        return response()->json($station->load('field.farm'), 201);
    }

    public function show(Station $station): JsonResponse
    {
        return response()->json($station->load(['field.farm', 'sensorData' => function($query) {
            $query->latest()->limit(100);
        }]));
    }

    public function update(Request $request, Station $station): JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => 'sometimes|string|unique:stations,serial_number,' . $station->id,
            'field_id' => 'sometimes|exists:fields,id',
            'expected_location' => 'nullable|array',
            'is_active' => 'sometimes|boolean'
        ]);

        $station->update($validated);
        return response()->json($station->load('field.farm'));
    }

    public function destroy(Station $station): JsonResponse
    {
        $station->delete();
        return response()->json(['message' => 'Станция удалена']);
    }

    public function data(Station $station): JsonResponse
    {
        $data = $station->sensorData()
            ->orderBy('timestamp', 'desc')
            ->paginate(50);
        
        return response()->json($data);
    }

    public function moveStation(Request $request, Station $station): JsonResponse
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id'
        ]);

        $station->update(['field_id' => $validated['field_id']]);
        return response()->json([
            'message' => 'Станция успешно перенесена',
            'station' => $station->load('field.farm')
        ]);
    }
} 