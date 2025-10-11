<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class)->only(['index','store','update','destroy']);
    Route::post('tasks/{task}/toggle', [TaskController::class, 'toggle']);
    Route::post('tasks/reorder', [TaskController::class, 'reorder']);
    Route::get('search', [TaskController::class, 'search']);
    Route::get('me', [AuthController::class, 'me']);
});
