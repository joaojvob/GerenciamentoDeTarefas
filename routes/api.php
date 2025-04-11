<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarefaController;

Route::get('/tarefas', [TarefaController::class, 'apiIndex']);
Route::post('/tarefas', [TarefaController::class, 'apiStore']);
Route::patch('/tarefas/{tarefa}', [TarefaController::class, 'apiUpdate']);
