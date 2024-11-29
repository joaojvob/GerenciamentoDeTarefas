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
        $tasks = Task::query()->where('user_id', auth()->id())->get();

        return datatables()->of($tasks)
            ->addColumn('actions', function ($task) {
                return '
                <a href="/tasks/' . $task->id . '" class="btn btn-sm btn-info">View</a>
                <a href="/tasks/' . $task->id . '/edit" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger delete-task" data-id="' . $task->id . '">Delete</button>
            ';
            })
            ->make(true);
    }

    // Exibir formulário de criação
    public function create()
    {
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

    // Exibir tarefa
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    // Exibir formulário de edição
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
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

        return redirect()->route('dashboard')->with('success', 'Task updated successfully!');
    }

    // Excluir tarefa
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(['success' => 'Task deleted successfully!']);
    }
}
