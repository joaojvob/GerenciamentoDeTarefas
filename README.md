
---

### README.md - Backend (Laravel)

```markdown
# app_tarefas_backend

[![PHP](https://img.shields.io/badge/PHP-8+-blue.svg)](https://php.net) [![Laravel](https://img.shields.io/badge/Laravel-10-red.svg)](https://laravel.com) [![MySQL](https://img.shields.io/badge/MySQL-5.7+-yellow.svg)](https://mysql.com)

API RESTful para gerenciamento de tarefas, desenvolvida com Laravel. Suporta autenticação de usuários e operações CRUD para tarefas.

## Sobre o Projeto
Este é o backend de um sistema de gerenciamento de tarefas, construído com Laravel. Ele fornece endpoints para autenticação, criação, leitura, atualização e exclusão de tarefas, integrado a um banco de dados MySQL.

## Funcionalidades
- **Autenticação**:
  - Login e registro com tokens via Laravel Sanctum.
  - Logout para invalidar tokens.
- **Gerenciamento de Tarefas**:
  - CRUD completo (create, read, update, delete).
  - Tarefas associadas ao usuário autenticado.
  - Ordenação por `ordem`.
- **Segurança**:
  - Rotas protegidas por middleware `auth:sanctum`.
  - Validação de entrada em todos os endpoints.

## Tecnologias
- **PHP**: 8+
- **Laravel**: 10
- **MySQL**: 5.7+
- **Autenticação**: Laravel Sanctum
- **Ferramentas**: Composer, Git

## Configuração
### Pré-requisitos
- PHP 8+
- Composer instalado ([Guia](https://getcomposer.org)).
- MySQL 5.7+
- Git

### Passos
1. **Clone o Repositório**:
   ```bash
   git clone https://github.com/seu-usuario/app_tarefas_backend.git
   cd app_tarefas_backend
