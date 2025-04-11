<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarefa;

class ApiController extends Controller{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user  = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
        }

        return response()->json(['error' => 'Credenciais inválidas'], 401);
    }

    public function apiIndex(Request $request)
    {
        $tarefas = Tarefa::where('user_id', auth()->id())->get();
        return response()->json($tarefas);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date',
            'prioridade'      => 'nullable|in:Baixa,Média,Alta',
            'status'          => 'nullable|in:Pendente,Em Andamento,Concluida',
        ]);

        $tarefa = Tarefa::create([
            'user_id'         => auth()->id(),
            'titulo'          => $request->titulo,
            'descricao'       => $request->descricao,
            'data_vencimento' => $request->data_vencimento,
            'prioridade'      => $request->prioridade ?? 'Média',
            'status'          => $request->status ?? 'Pendente',
        ]);

        return response()->json($tarefa, 201);
    }

    public function apiUpdate(Request $request, Tarefa $tarefa)
    {
        Tarefa::authorizeTarefa($tarefa);

        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date',
            'prioridade'      => 'nullable|in:Baixa,Média,Alta',
            'status'          => 'nullable|in:Pendente,Em Andamento,Concluida',
        ]);

        $tarefa->update($request->only(['titulo', 'descricao', 'data_vencimento', 'prioridade', 'status']));

        return response()->json($tarefa);
    }
}