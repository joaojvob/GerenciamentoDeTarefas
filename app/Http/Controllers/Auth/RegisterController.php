<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index()
    {
        return view('authentication.auth');
    }

    // Registrar novo usuário
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criar novo usuário
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('authentication.auth')->with('success', 'Cadastro realizado com sucesso! Faça login.');
    }

    // Função para exibir a página de login
    public function showLoginForm()
    {
        return view('authentication.auth');
    }

    // Função para validar o login
    public function login(Request $request)
    {
        // Validação dos campos
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $message = 'entrei no controller ';
        error_log($message);
        error_log($request);

        // Verificar credenciais do usuário
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Login bem-sucedido
            return response()->json([
                'success' => true,
                'redirect_url' => route('home')
            ]);
        } else {
            // Credenciais incorretas
            return response()->json([
                'success' => false,
                'message' => 'As credenciais não coincidem com nossos registros.'
            ], 401);
        }
    }


    // Função para logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('authentication.auth')->with('success', 'Você saiu com sucesso.');
    }
}
