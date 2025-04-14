<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Tarefas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .description {
            max-width: 300px;  
            word-wrap: break-word;  
            overflow-wrap: break-word; 
            white-space: normal; 
        }
        .summary {
            margin-bottom: 20px;
            font-size: 14px;
        }
        p {
            margin: 5px 0;
        }
        tr {
            page-break-inside: avoid;
        }
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
                    <td>{{ $tarefa->titulo ?? 'Sem título' }}</td>
                    <td class="description">{{ $tarefa->descricao ?? 'Sem descrição' }}</td>
                    <td>{{ $tarefa->data_vencimento_formatada }}</td>
                    <td>{{ $tarefa->prioridade ?? 'Não definido' }}</td>
                    <td>{{ $tarefa->status ?? 'Não definido' }}</td>
                    @if($is_admin)
                        <td>{{ $tarefa->user->name ?? 'Desconhecido' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>