<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tarefas', [ApiController::class, 'apiIndex']);
    Route::post('/tarefas', [ApiController::class, 'apiStore']);
    Route::patch('/tarefas/{tarefa}', [ApiController::class, 'apiUpdate']);
    Route::delete('/tarefas/{tarefa}', [ApiController::class, 'apiDestroy']);
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::patch('/update-password', [ApiController::class, 'updatePassword']);
});