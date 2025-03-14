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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PreventiveMaintenanceReportController;
use App\Http\Controllers\CommentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/test', function () {
    return response()->file(storage_path('app/public/proofs/1730102505_proof.jpg'));
});

Route::get('/storage/proofs/{filename}', function ($filename) {
    $filePath = storage_path("app/public/proofs/{$filename}");

    // Check if the file exists
    if (file_exists($filePath)) {
        return response()->file($filePath);  // Return the image
    }

    // Return 404 if the file is not found
    return response()->json(['error' => 'File not found'], 404);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::get('/getUserByType/{type}', [UserController::class, 'getUserByType']);
    Route::post('/logout', [UserController::class, 'logout']);

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
    // Route::put('/updateTask/{id}', [TaskController::class, 'updateTask']);
    Route::put('/updateTaskStatus/{id}', [TaskController::class, 'updateTaskStatus']);
    Route::post('/uploadProof/{id}', [TaskController::class, 'uploadProof']);

    // notification
    
    Route::apiResource('/notifications', NotificationController::class);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // inventory
    Route::apiResource('/inventory', InventoryController::class);

    // Preventive maintenance

    Route::apiResource('/preventive-maintenance', PreventiveMaintenanceController::class);
    Route::put('/updatePreventiveStatus/{id}', [PreventiveMaintenanceController::class, 'updatePreventiveStatus']);
    Route::get('/getMyPreventiveMaintenanceTasks', [PreventiveMaintenanceController::class, 'getMyPreventiveMaintenanceTasks']);
    
    Route::apiResource('preventive-maintenance-report', PreventiveMaintenanceReportController::class);
    Route::get('/getReportsByPreventiveId/{preventiveId}', [PreventiveMaintenanceReportController::class, 'getReportsByPreventiveId']);
    // Other protected routes

    // COMMENTS
    
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{service_request_id}', [CommentController::class, 'index']);

});

// Create API routes for User CRUD

