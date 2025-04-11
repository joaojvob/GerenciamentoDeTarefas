<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarefaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/tarefas/{tarefa}/atualiza', [TarefaController::class, 'atualiza'])->name('tarefas.atualiza');
    Route::any('/tarefas/data', [TarefaController::class, 'data'])->name('tarefas.data');
    Route::get('/tarefas/relatorio', [TarefaController::class, 'relatorio']);

    Route::resource('tarefas', TarefaController::class);
});

require __DIR__ . '/auth.php';
