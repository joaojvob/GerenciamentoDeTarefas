<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Suas Tarefas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-4">
                        <button class="bg-blue-500 text-white py-2 px-4 rounded" id="createTarefaButton">
                            Criar Tarefa
                        </button>
                        <div class="flex items-center">
                            <button class="bg-gray-500 text-white py-2 px-4 rounded mr-2" id="reloadTarefas">
                                Atualizar
                            </button>
                            <div class="flex items-center">
                                <select id="reportFormat" class="border rounded p-2 mr-2 text-gray-900 bg-white dark:bg-gray-700 dark:text-gray-200">
                                    <option value="pdf">PDF</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="docx">DOCX</option>
                                </select>
                                <button id="exportReport" class="bg-green-500 text-white py-2 px-4 rounded">
                                    Exportar Relatório
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold">Análise de Produtividade</h3>
                        <div class="mb-4">
                            <label for="periodSelect">Período:</label>
                            <select id="periodSelect" class="border rounded p-1 text-gray-900 bg-white dark:bg-gray-700 dark:text-gray-200">
                                <option value="week">Semanal</option>
                                <option value="month">Mensal</option>
                            </select>
                        </div>
                        <canvas id="productivityChart" height="100"></canvas>
                    </div>

                    <table id="tarefasTable" class="table-auto w-full">
                        <thead>
                            <tr>
                                <th>Ordem</th>
                                <th>Título</th>
                                <th>Data de Vencimento</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
        window.tarefaRoutes = {
            base: '{{ route("tarefas.data") }}',
            store: '{{ route("tarefas.store") }}',
            analise: '{{ route("tarefas.analise") }}',
            relatorio: '{{ route("tarefas.relatorio", ":format") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    <script src="{{ asset('js/tarefa.js') }}"></script>

    @include('tarefas.show')
    @include('tarefas.form')
</x-app-layout>