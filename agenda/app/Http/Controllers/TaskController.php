<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Listar tarefas
    public function dataTable()
    {
        return view('dashboard');
    }

    public function data(Request $request)
    {
        // Buscar as tarefas apenas do usuário logado
        $tasks = Task::where('user_id', auth()->id())->get();

        return datatables()->of($tasks)->make(true);
    }

    // Exibir formulário de criação
    public function create()
    {
        dd(2);
        return view('tasks.create');
    }

    // Armazenar tarefa
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date',
            'end_time'    => 'nullable|date',
        ]);

        Task::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return redirect()->route('dashboard')->with('success', 'Task added successfully!');
    }
    // Exibir formulário de edição
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return response()->json($task);
    }

    // Atualizar tarefa
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date',
            'end_time'    => 'nullable|date',
        ]);

        $task->update([
            'title'       => $request->title,
            'description' => $request->description,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return response()->json(['success' => 'Task updated successfully!']);
    }

    // Excluir tarefa
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['success' => 'Task deleted successfully!']);
    }
}
