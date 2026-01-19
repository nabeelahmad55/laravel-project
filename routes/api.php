<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TeamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // Teams
        Route::apiResource('teams', TeamController::class);
        Route::post('/teams/{team}/invite', [TeamController::class, 'inviteMember']);
        
        // Projects
        Route::apiResource('projects', ProjectController::class);
        Route::get('/teams/{team}/projects', [ProjectController::class, 'teamProjects']);
        
        // Tasks
        Route::apiResource('tasks', TaskController::class);
        Route::post('/tasks/reorder', [TaskController::class, 'reorder']);
        Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
        Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment']);
        Route::post('/tasks/{task}/attachments', [TaskController::class, 'addAttachment']);
        
        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/upcoming-tasks', [DashboardController::class, 'upcomingTasks']);
    });
});