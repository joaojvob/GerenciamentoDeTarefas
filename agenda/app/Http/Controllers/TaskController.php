<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function dataTable()
    {
        return view('dashboard');
    }

    public function data(Request $request)
    {
        if (auth()->user()->is_admin) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', auth()->id())->get();
        }

        return datatables()->of($tasks)->make(true);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date_format:Y-m-d\TH:i',
            'end_time'    => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $existingTask = Task::where('user_id', auth()->id())
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->first();


        if ($existingTask) {
            return response()->json(['error' => 'Já existe uma tarefa para este dia e horário.'], 422);
        }

        Task::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tarefa criada com sucesso!');
    }

    public function atualiza(Request $request, Task $task)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'nullable|date_format:Y-m-d\TH:i',
            'end_time'    => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $existingTask = Task::where('user_id', auth()->id())
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->where('id', '!=', $task->id)
            ->first();

        if ($existingTask) {
            return response()->json(['error' => 'Já existe uma tarefa para este dia e horário.'], 422);
        }

        $task->update([
            'title'       => $request->title,
            'description' => $request->description,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return response()->json(['success' => 'Tarefa atualizada com sucesso!']);
    }

    public function edit(Task $task)
    {
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['success' => 'Tarefa excluída com sucesso!']);
    }
}
