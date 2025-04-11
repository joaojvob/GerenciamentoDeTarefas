<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Tarefas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Relatório de Tarefas</h1>
    <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    <p>Usuário: {{ auth()->user()->name }} {{ $is_admin ? '(Administrador)' : '' }}</p>

    <div class="summary">
        <p><strong>Total de Tarefas:</strong> {{ $total_tarefas }}</p>
        <p><strong>Tarefas Concluídas:</strong> {{ $tarefas_concluidas }}</p>
        <p><strong>Percentual Concluído:</strong> {{ number_format($percentual_concluido, 2) }}%</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data de Vencimento</th>
                <th>Prioridade</th>
                <th>Status</th>
                @if($is_admin)
                    <th>Usuário</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($tarefas as $tarefa)
                <tr>
                    <td>{{ $tarefa->titulo }}</td>
                    <td>{{ $tarefa->descricao ?? '-' }}</td>
                    <td>{{ $tarefa->data_vencimento ? \Carbon\Carbon::parse($tarefa->data_vencimento)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ ucfirst($tarefa->prioridade) }}</td>
                    <td>{{ ucfirst($tarefa->status) }}</td>
                    @if($is_admin)
                        <td>{{ $tarefa->user->name ?? 'N/A' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>