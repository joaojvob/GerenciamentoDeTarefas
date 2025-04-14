<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user  = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'token'   => $token,
                'user'    => $user,
                'message' => 'Login realizado com sucesso!'
            ], 200);
        }
        return response()->json(['message' => 'Credenciais inválidas. Verifique seu email ou senha.'], 401);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'user'    => $user,
            'message' => 'Usuário registrado com sucesso! Bem-vindo(a).'
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso. Até logo!']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'A senha atual está incorreta.'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Senha atualizada com sucesso!']);
    }

    public function apiIndex(Request $request)
    {
        $tarefas = Tarefa::where('user_id', auth()->id())->orderBy('ordem')->get();
        
        return response()->json([
            'data'    => $tarefas,
            'message' => 'Tarefas carregadas com sucesso.'
        ], 200);
    }

    public function apiShow(Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);

        return response()->json([
            'data'    => $tarefa,
            'message' => 'Tarefa encontrada com sucesso.'
        ], 200);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:255',
            'descricao'       => 'nullable|string',
            'data_vencimento' => 'nullable|date',
            'prioridade'      => 'nullable|in:baixa,media,alta',
            'status'          => 'nullable|in:pendente,em_andamento,concluida',
            'ordem'           => 'nullable|integer',
        ]);

        $tarefa = Tarefa::create([
            'user_id'         => auth()->id(),
            'titulo'          => $request->titulo,
            'descricao'       => $request->descricao,
            'data_vencimento' => $request->data_vencimento,
            'prioridade'      => $request->prioridade ?? 'media',
            'status'          => $request->status ?? 'pendente',
            'ordem'           => $request->ordem ?? Tarefa::where('user_id', auth()->id())->max('ordem') + 1,
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
            'ordem'           => 'nullable|integer',
        ]);

        $tarefa->update($request->only(['titulo', 'descricao', 'data_vencimento', 'prioridade', 'status', 'ordem']));

        return response()->json([
            'data'    => $tarefa,
            'message' => 'Tarefa atualizada com sucesso!'
        ], 200);
    }

    public function apiDestroy(Tarefa $tarefa)
    {
        $this->authorizeTarefa($tarefa);
        $tarefa->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso!'], 200);
    }

    protected function authorizeTarefa(Tarefa $tarefa)
    {
        if ($tarefa->user_id !== auth()->id()) {
            abort(403, 'Ação não autorizada.');
        }
    }
}