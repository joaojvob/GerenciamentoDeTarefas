<!DOCTYPE html>
<html lang="pt-BR" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token CSRF -->
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Arvo" rel="stylesheet">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
</head>

<body>
    <div class="container" id="container">
        <!-- Formulário de Criação de Conta -->
        <div class="form-container sign-up-container">
            <form id="createForm" action="{{ route('auth.create') }}" method="POST" data-create-url="{{ route('auth.create') }}">
                @csrf

                <h1>Criar Conta</h1>
                <input type="text" id="nome" placeholder="Nome" />
                <input type="email" id="email" placeholder="Email" />
                <input type="password" id="password" placeholder="Senha" />
                <button type="submit">Inscreva-se</button>
                <div id="createMessage"></div>
            </form>
        </div>

        <!-- Formulário de Login -->
        <div class="form-container sign-in-container">
            <form id="loginForm" action="{{ route('auth.login') }}" method="POST" data-login-url="{{ route('auth.login') }}">
                @csrf

                <h1>Entrar</h1>
                <input type="email" id="email" placeholder="Email" />
                <input type="password" id="password" placeholder="Senha" />
                <a href="#">Esqueceu a senha?</a>
                <button type="submit">Entrar</button>
                <div id="loginMessage"></div>
            </form>
        </div>

        <!-- Painel de Alternância -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Bem-vindo de volta!</h1>
                    <p>Para se manter conectado conosco, faça login com seus dados pessoais</p>
                    <button class="ghost" id="signIn">Entrar</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Olá!</h1>
                    <p>Insira seus dados pessoais para abrir uma conta conosco</p>
                    <button class="ghost" id="signUp">Criar Conta</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="{{ asset('js/auth/auth.js') }}"></script>
</body>

</html>
