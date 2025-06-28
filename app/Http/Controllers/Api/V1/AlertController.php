<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(): JsonResponse
    {
        $alerts = Alert::with(['station.field.farm'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json($alerts);
    }

    public function show(Alert $alert): JsonResponse
    {
        return response()->json($alert->load(['station.field.farm', 'sensorData']));
    }

    public function update(Request $request, Alert $alert): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:new,acknowledged,resolved',
            'notes' => 'nullable|string'
        ]);

        $alert->update($validated);
        return response()->json($alert->load('station.field.farm'));
    }

    public function destroy(Alert $alert): JsonResponse
    {
        $alert->delete();
        return response()->json(['message' => 'Уведомление удалено']);
    }
} 