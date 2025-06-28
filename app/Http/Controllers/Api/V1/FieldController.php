<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(): JsonResponse
    {
        $fields = Field::with(['farm', 'currentCrop.crop'])->get();
        return response()->json($fields);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area_hectares' => 'required|numeric|min:0',
            'farm_id' => 'required|exists:farms,id',
            'boundary' => 'nullable|array'
        ]);

        $field = Field::create($validated);
        return response()->json($field->load('farm'), 201);
    }

    public function show(Field $field): JsonResponse
    {
        return response()->json($field->load([
            'farm', 
            'stations', 
            'currentCrop.crop',
            'cropRotations.crop' => function($query) {
                $query->orderBy('planting_date', 'desc');
            }
        ]));
    }

    public function update(Request $request, Field $field): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'area_hectares' => 'sometimes|numeric|min:0',
            'farm_id' => 'sometimes|exists:farms,id',
            'boundary' => 'nullable|array'
        ]);

        $field->update($validated);
        return response()->json($field->load('farm'));
    }

    public function destroy(Field $field): JsonResponse
    {
        $field->delete();
        return response()->json(['message' => 'Поле удалено']);
    }
} 