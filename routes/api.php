<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::post('/tasks/{id}/subtasks', [TaskController::class, 'createSubtask']);
});
