<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\BrowserSessionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\StationController;
use App\Http\Controllers\Api\V1\FieldController;
use App\Http\Controllers\Api\V1\FarmController;
use App\Http\Controllers\Api\V1\AlertController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\StationDataController;
use App\Http\Controllers\Api\V1\CropController;
use App\Http\Controllers\Api\V1\CropRotationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ForgotPasswordController;


Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

// API для станций (без аутентификации)
Route::post('v1/station-data', [StationDataController::class, 'store']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::apiResource('users', UserController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);
    Route::get('category-list', [CategoryController::class, 'getList']);
    Route::get('/user', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

    // Browser Sessions
    Route::get('browser-sessions', [BrowserSessionController::class, 'index']);
    Route::post('logout-other-devices', [BrowserSessionController::class, 'logoutOtherDevices']);

    // Activity log
    Route::get('activity-logs', ActivityLogController::class);

    // Сельскохозяйственная система API
    Route::prefix('v1')->group(function () {
        Route::apiResource('stations', StationController::class);
        Route::get('stations/{station}/data', [StationController::class, 'data']);
        Route::apiResource('farms', FarmController::class);
        Route::apiResource('fields', FieldController::class);
        Route::apiResource('alerts', AlertController::class)->except(['store']);
        Route::apiResource('crops', CropController::class);
        Route::apiResource('crop-rotations', CropRotationController::class);
        Route::get('fields/{field}/crop-rotations', [CropRotationController::class, 'fieldRotations']);
        Route::get('dashboard', [DashboardController::class, 'index']);
    });

    Route::get('abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
});

Route::get('category-list', [CategoryController::class, 'getList']);
Route::get('get-posts', [PostController::class, 'getPosts']);
Route::get('get-category-posts/{id}', [PostController::class, 'getCategoryByPosts']);
Route::get('get-post/{id}', [PostController::class, 'getPost']);
