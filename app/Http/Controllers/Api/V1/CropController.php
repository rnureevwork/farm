<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function index(): JsonResponse
    {
        $crops = Crop::orderBy('name')->get();
        return response()->json($crops);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:crops',
            'description' => 'nullable|string'
        ]);

        $crop = Crop::create($validated);
        return response()->json($crop, 201);
    }

    public function show(Crop $crop): JsonResponse
    {
        return response()->json($crop);
    }

    public function update(Request $request, Crop $crop): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:crops,name,' . $crop->id,
            'description' => 'nullable|string'
        ]);

        $crop->update($validated);
        return response()->json($crop);
    }

    public function destroy(Crop $crop): JsonResponse
    {
        $crop->delete();
        return response()->json(['message' => 'Культура удалена']);
    }
} 