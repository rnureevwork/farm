<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\Field;
use App\Models\Alert;
use App\Models\SensorData;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'total_stations' => Station::count(),
            'active_stations' => Station::where('is_active', true)->count(),
            'total_fields' => Field::count(),
            'total_alerts' => Alert::count(),
            'new_alerts' => Alert::where('status', 'new')->count(),
            'total_farms' => DB::table('farms')->count(),
        ];

        $recentAlerts = Alert::with(['station.field.farm'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stationStatus = Station::select('is_active', DB::raw('count(*) as count'))
            ->groupBy('is_active')
            ->get()
            ->pluck('count', 'is_active')
            ->toArray();

        $latestData = SensorData::with(['station.field.farm'])
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_alerts' => $recentAlerts,
            'station_status' => $stationStatus,
            'latest_data' => $latestData
        ]);
    }
} 