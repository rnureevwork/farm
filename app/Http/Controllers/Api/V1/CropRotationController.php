<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CropRotation;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropRotationController extends Controller
{
    public function index(): JsonResponse
    {
        $cropRotations = CropRotation::with(['field.farm', 'crop'])
            ->orderBy('planting_date', 'desc')
            ->get();
        return response()->json($cropRotations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_id' => 'required|exists:crops,id',
            'planting_date' => 'required|date',
            'harvest_date' => 'nullable|date|after:planting_date'
        ]);

        $cropRotation = CropRotation::create($validated);
        return response()->json($cropRotation->load(['field.farm', 'crop']), 201);
    }

    public function show(CropRotation $cropRotation): JsonResponse
    {
        return response()->json($cropRotation->load(['field.farm', 'crop']));
    }

    public function update(Request $request, CropRotation $cropRotation): JsonResponse
    {
        $validated = $request->validate([
            'crop_id' => 'sometimes|required|exists:crops,id',
            'planting_date' => 'sometimes|required|date',
            'harvest_date' => 'nullable|date|after:planting_date'
        ]);

        $cropRotation->update($validated);
        return response()->json($cropRotation->load(['field.farm', 'crop']));
    }

    public function destroy(CropRotation $cropRotation): JsonResponse
    {
        $cropRotation->delete();
        return response()->json(['message' => 'Севооборот удален']);
    }

    public function fieldRotations(Field $field): JsonResponse
    {
        $cropRotations = $field->cropRotations()
            ->with('crop')
            ->orderBy('planting_date', 'desc')
            ->get();
        return response()->json($cropRotations);
    }
} 