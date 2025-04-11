<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tarefas', [ApiController::class, 'apiIndex']);
    Route::post('/tarefas', [ApiController::class, 'apiStore']);
    Route::patch('/tarefas/{tarefa}', [ApiController::class, 'apiUpdate']);
});