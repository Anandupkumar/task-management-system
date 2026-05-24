<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth'); // use default session auth instead of sanctum

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::patch('/tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
    Route::get('/tasks/{task}/ai-summary', [TaskApiController::class, 'aiSummary']);
});
