# Agenda Laravel - Documentação

## Sobre o Projeto
Este é um projeto de uma Agenda desenvolvida utilizando **PHP** e **MySQL** em conjunto com o **Framework Laravel**. O objetivo principal é demonstrar habilidades em padrões de desenvolvimento como **Orientação a Objetos (OO)** e **MVC** (Model-View-Controller), além de criar interfaces intuitivas e funcionais com **Blade Templates**.

---

## Funcionalidades
- **Sistema Multiusuário**:
  - Tela de login com autenticação segura.
  - Cadastro de usuários.

- **Gerenciamento de Tarefas**:
  - Cadastro de tarefas associado a usuários.
  - Validação de conflitos: é proibido cadastrar tarefas no mesmo dia e horário para o mesmo usuário.

- **Interface Intuitiva**:
  - Layout responsivo utilizando **Bootstrap** (ou outra biblioteca de sua escolha).
  - Exibição organizada das tarefas agendadas por usuário.

- **APIs e Rotas**:
  - Nomenclatura intuitiva para rotas, facilitando identificação e manutenção.
  - Possibilidade de uso de **Javascript**, **jQuery** e **Ajax** para interatividade.

---

## Tecnologias Utilizadas
- **Backend**:
  - PHP 8+
  - Laravel 10
- **Frontend**:
  - Blade Templates
  - Bootstrap / TailwindCSS (ou outra biblioteca CSS de preferência)
  - Javascript / jQuery (opcional)
- **Banco de Dados**:
  - MySQL
- **Versionamento de Código**:
  - Git e GitHub

---

## Configuração do Ambiente
### Requisitos
1. PHP 8+
2. Composer
3. MySQL 5.7+
4. Node.js (opcional para compilação de assets)
5. Git

### Passos para Configuração
1. Clone este repositório:
   ```bash
   git clone https://github.com/seu-usuario/agenda-laravel.git
   ```

2. Acesse o diretório do projeto:
   ```bash
   cd agenda-laravel
   ```

3. Instale as dependências do Laravel:
   ```bash
   composer install
   ```

4. Configure o arquivo `.env`:
   - Renomeie o arquivo `.env.example` para `.env`.
   - Atualize as credenciais do banco de dados:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=agenda
     DB_USERNAME=seu-usuario
     DB_PASSWORD=sua-senha
     ```

5. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

6. Execute as migrações para criar as tabelas no banco de dados:
   ```bash
   php artisan migrate
   ```

7. Inicie o servidor local:
   ```bash
   php artisan serve
   ```
   A aplicação estará disponível em [http://localhost:8000](http://localhost:8000).

---

## Estrutura de Banco de Dados
Segue o diagrama de entidades e relacionamentos utilizado no projeto:

- **Usuários** (`users`)
  - `id`: Identificador único (PK)
  - `name`: Nome do usuário
  - `email`: Endereço de e-mail (unique)
  - `password`: Senha criptografada
  - `created_at`, `updated_at`

- **Tarefas** (`tasks`)
  - `id`: Identificador único (PK)
  - `user_id`: Relacionamento com o usuário (FK)
  - `title`: Título da tarefa
  - `description`: Descrição detalhada
  - `date`: Data da tarefa
  - `time`: Horário da tarefa
  - `created_at`, `updated_at`

---

## Uso do Sistema
### Acesso ao Sistema
1. Realize o cadastro de um usuário na página de **Registro**.
2. Utilize suas credenciais para acessar o sistema.

### Gerenciamento de Tarefas
1. Acesse a página de **Tarefas** no menu principal.
2. Cadastre uma nova tarefa, informando:
   - Título
   - Descrição
   - Data e Hora
3. Confira a lista de tarefas agendadas na tela principal.
4. Utilize as opções de edição e exclusão conforme necessário.

### Validações
- Tarefas com data e hora conflitantes para o mesmo usuário não serão permitidas.

---

## Versionamento
As modificações no projeto foram versionadas utilizando o **Git** e publicadas no **GitHub**. Utilize os seguintes comandos para contribuir:

1. Crie um novo branch para sua funcionalidade ou correção:
   ```bash
   git checkout -b minha-nova-feature
   ```

2. Realize os commits das alterações:
   ```bash
   git commit -m "Adiciona nova funcionalidade"
   ```

3. Envie suas alterações para o repositório remoto:
   ```bash
   git push origin minha-nova-feature
   ```

4. Crie um **Pull Request** no GitHub para revisão.

---

## Contribuição
Contribuições são bem-vindas! Siga as diretrizes do projeto e abra um Pull Request com sua sugestão ou correção.

---

## Licença
Este projeto está licenciado sob a [MIT License](LICENSE).

