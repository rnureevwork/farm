<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FarmController extends Controller
{
    /**
     * Получить список всех ферм
     */
    public function index(): JsonResponse
    {
        $farms = Farm::with('fields')->get();
        return response()->json($farms);
    }

    /**
     * Получить конкретную ферму
     */
    public function show(Farm $farm): JsonResponse
    {
        $farm->load([
            'fields.stations',
            'fields.currentCrop.crop',
            'users'
        ]);
        return response()->json($farm);
    }

    /**
     * Создать новую ферму
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $farm = Farm::create($request->all());
        return response()->json($farm, 201);
    }

    /**
     * Обновить ферму
     */
    public function update(Request $request, Farm $farm): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $farm->update($request->all());
        return response()->json($farm);
    }

    /**
     * Удалить ферму
     */
    public function destroy(Farm $farm): JsonResponse
    {
        $farm->delete();
        return response()->json(null, 204);
    }
} 