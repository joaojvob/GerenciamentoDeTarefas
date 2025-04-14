<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarefaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.register');
});

Route::get('/dashboard', [TarefaController::class, 'dataTable'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tarefas/data', [TarefaController::class, 'data'])->name('tarefas.data');
    Route::get('/tarefas/relatorio/pdf', [TarefaController::class, 'generatePdfReport'])->name('tarefas.relatorio.pdf');
    Route::get('/tarefas/analise-produtividade', [TarefaController::class, 'productivityAnalysis'])->name('tarefas.analise');
    Route::patch('/tarefas/{id}/update', [TarefaController::class, 'update'])->name('tarefas.update');
    Route::get('/tarefas/{id}', [TarefaController::class, 'show'])->name('tarefas.show');

    Route::get('/tarefas/create', [TarefaController::class, 'create'])->name('tarefas.create');
    Route::post('/tarefas', [TarefaController::class, 'store'])->name('tarefas.store');
    Route::get('/tarefas/{tarefa}/edit', [TarefaController::class, 'edit'])->name('tarefas.edit');
    Route::delete('/tarefas/{tarefa}', [TarefaController::class, 'destroy'])->name('tarefas.destroy');
});

require __DIR__ . '/auth.php';