@component('mail::message')
# Lembrete de Tarefa

Olá, {{ $tarefa->user->name }},

Sua tarefa **{{ $tarefa->titulo }}** está próxima do vencimento:

- **Descrição**: {{ $tarefa->descricao ?? 'Sem descrição' }}
- **Data de Vencimento**: {{ \Carbon\Carbon::parse($tarefa->data_vencimento)->format('d/m/Y H:i') }}
- **Prioridade**: {{ ucfirst($tarefa->prioridade) }}
- **Status**: {{ ucfirst(str_replace('_', ' ', $tarefa->status)) }}

Por favor, verifique sua tarefa no aplicativo.

Obrigado,  
Equipe App Tarefas

@component('mail::button', ['url' => env('APP_URL', 'http://localhost:8000')])
Acessar App
@endcomponent
@endcomponent