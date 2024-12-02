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

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('authentication.auth')->with('success', 'Cadastro realizado com sucesso! Faça login.');
    }

    public function showLoginForm()
    {
        return view('authentication.auth');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $message = 'entrei no controller ';
        error_log($message);
        error_log($request);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            return response()->json([
                'success' => true,
                'redirect_url' => route('home')
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'As credenciais não coincidem com nossos registros.'
            ], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('authentication.auth')->with('success', 'Você saiu com sucesso.');
    }
}
