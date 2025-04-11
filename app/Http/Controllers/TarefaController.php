<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarefa;
use Illuminate\Support\Facades\DB;

class TarefaController extends Controller
{
    public function dataTable()
    {
        return view('dashboard');
    }

    public function data(Request $request)
    {
        $query = Tarefa::query();
        
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        return datatables()->of($query)
            ->addColumn('actions', function ($tarefa) {
                return '
                    <button class="bg-green-500 text-white px-2 py-1 rounded item-edit" data-id="'.$tarefa->id.'">Editar</button>
                    <button class="bg-red-500 text-white px-2 py-1 rounded item-remove" data-id="'.$tarefa->id.'">Excluir</button>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('tarefas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date_format:Y-m-d\TH:i',
            'prioridade'      => 'nullable|in:baixa,media,alta',
            'status'          => 'nullable|in:pendente,em_andamento,concluida',
        ]);

        $conflito = Tarefa::where('user_id', auth()->id())
            ->where('data_vencimento', $request->data_vencimento)
            ->exists();

        if ($conflito) {
            return response()->json(['error' => 'Já existe uma tarefa neste horário.'], 422);
        }

        $tarefa = Tarefa::create([
            'user_id'         => auth()->id(),
            'titulo'          => $request->titulo,
            'descricao'       => $request->descricao,
            'data_vencimento' => $request->data_vencimento,
            'prioridade'      => $request->prioridade ?? 'media',
            'status'          => $request->status ?? 'pendente',
            'ordem'           => Tarefa::where('user_id', auth()->id())->max('ordem') + 1,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tarefa criada com sucesso!');
    }

    public function atualiza(Request $request, Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);

        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date_format:Y-m-d\TH:i',
            'prioridade'      => 'nullable|in:baixa,media,alta',
            'status'          => 'nullable|in:pendente,em_andamento,concluida',
        ]);

        $conflito = Tarefa::where('user_id', auth()->id())
            ->where('data_vencimento', $request->data_vencimento)
            ->where('id', '!=', $tarefa->id)
            ->exists();

        if ($conflito) {
            return response()->json(['error' => 'Já existe uma tarefa neste horário.'], 422);
        }

        $tarefa->update($request->only(['titulo', 'descricao', 'data_vencimento', 'prioridade', 'status']));

        return response()->json(['success' => 'Tarefa atualizada com sucesso!']);
    }

    public function edit(Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);
        return response()->json($tarefa);
    }

    public function destroy(Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);
        $tarefa->delete();
        return response()->json(['success' => 'Tarefa excluída com sucesso!']);
    }

    // Nova função para API do app
    public function apiIndex(Request $request)
    {
        $tarefas = Tarefa::where('user_id', 1)->get();
        return response()->json($tarefas);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date',
            'prioridade'      => 'nullable|in:baixa,media,alta',
            'status'          => 'nullable|in:pendente,em_andamento,concluida',
        ]);

        $tarefa = Tarefa::create([
            'user_id'         => auth()->id(),
            'titulo'          => $request->titulo,
            'descricao'       => $request->descricao,
            'data_vencimento' => $request->data_vencimento,
            'prioridade'      => $request->prioridade ?? 'media',
            'status'          => $request->status ?? 'pendente',
        ]);

        return response()->json($tarefa, 201);
    }

    public function apiUpdate(Request $request, Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);

        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date',
            'prioridade'      => 'nullable|in:baixa,media,alta',
            'status'          => 'nullable|in:pendente,em_andamento,concluida',
        ]);

        $tarefa->update($request->only(['titulo', 'descricao', 'data_vencimento', 'prioridade', 'status']));
        return response()->json($tarefa);
    }

    public function relatorio()
    {
        $total = Tarefa::where('user_id', auth()->id())->count();
        $concluidas = Tarefa::where('user_id', auth()->id())->where('status', 'concluida')->count();

        return response()->json([
            'total_tarefas'        => $total,
            'tarefas_concluidas'   => $concluidas,
            'percentual_concluido' => $total > 0 ? ($concluidas / $total) * 100 : 0,
        ]);
    }

    private function authorizeTarefa(Tarefa $tarefa)
    {
        if (!auth()->user()->is_admin && $tarefa->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para esta ação.');
        }
    }
}