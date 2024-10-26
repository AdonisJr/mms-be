<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PreventiveMaintenanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/services', [ServiceController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::get('/getUserByType/{type}', [UserController::class, 'getUserByType']);

    // services
    // Route::post('/services', [ServiceController::class, 'store']);
    // Route::get('/services', [ServiceController::class, 'show']);
    // Route::put('/services', [ServiceController::class, 'update']);
    // Route::delete('/services', [ServiceController::class, 'destroy']);
    
    Route::apiResource('service', ServiceController::class);

    // service request
    Route::apiResource('service-requests', ServiceRequestController::class);
    Route::get('/getByCurrentUser', [ServiceRequestController::class, 'getByCurrentUser']);
    Route::post('/service-requests/{id}/assign-task', [ServiceRequestController::class, 'assignTask']);
    
    // task
    Route::apiResource('/tasks', TaskController::class);
    Route::get('/assignedToMe', [TaskController::class, 'assignedToMe']);
    Route::put('/updateTask/{id}', [TaskController::class, 'updateTask']);

    // inventory
    Route::apiResource('/inventory', InventoryController::class);

    // Preventive maintenance
    Route::apiResource('/preventive-maintenance', PreventiveMaintenanceController::class);
    Route::get('/getMyPreventiveMaintenanceTasks', [PreventiveMaintenanceController::class, 'getMyPreventiveMaintenanceTasks']);
    // Other protected routes
});

// Create API routes for User CRUD

