document.addEventListener("DOMContentLoaded", function() {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const loginForm = document.getElementById("loginForm");
    const createForm = document.getElementById("createForm");

    if (loginForm) {

        loginForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            const loginMessage = document.getElementById("loginMessage");

            const loginUrl = loginForm.dataset.loginUrl;

            debugger

            fetch(loginUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email: email, password: password })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro de autenticação');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loginMessage.innerHTML = '<div class="alert alert-success">Login bem-sucedido!</div>';
                    window.location.href = data.redirect_url || '/home';
                } else {
                    loginMessage.innerHTML = '<div class="alert alert-danger">Email ou senha incorretos!</div>';
                }
            })
            .catch(error => {
                debugger
                console.error('Erro:', error);
                loginMessage.innerHTML = '<div class="alert alert-danger">Ocorreu um erro ao tentar fazer login. Tente novamente mais tarde.</div>';
            });
        });
    } else {
        console.warn("loginForm não foi encontrado.");
    }

    if (createForm) {
        createForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const nome = document.getElementById("nome").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            const createMessage = document.getElementById("createMessage");

            const createUrl = createForm.dataset.createUrl;

            fetch(createUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nome: nome, email: email, password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    debugger
                    createMessage.innerHTML = '<div class="alert alert-success">Conta criada com sucesso!</div>';
                    window.location.href = data.redirect_url || '/home';
                } else {
                    createMessage.innerHTML = '<div class="alert alert-danger">Erro ao criar conta! Tente novamente.</div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                createMessage.innerHTML = '<div class="alert alert-danger">Ocorreu um erro ao tentar criar a conta. Tente novamente mais tarde.</div>';
            });
        });
    } else {
        console.warn("createForm não foi encontrado.");
    }

    const signUpButton = document.getElementById("signUp");
    const signInButton = document.getElementById("signIn");
    const container = document.getElementById("container");

    if (signUpButton && signInButton && container) {
        signUpButton.addEventListener("click", () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener("click", () => {
            container.classList.remove("right-panel-active");
        });
    } else {
        console.warn("Elementos de alternância de painel não foram encontrados.");
    }
});
