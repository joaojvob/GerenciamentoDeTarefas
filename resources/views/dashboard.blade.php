<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Suas tarefas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded mb-4" id="createTarefaButton">
                        Criar Tarefa
                    </button>

                    <table id="tarefasTable" class="table-auto w-full">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Data de Vencimento</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('tarefas.create', ['ajax' => true])
    @include('tarefas.edit', ['ajax' => true])

    <!-- Scripts Essenciais -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/js/tarefa.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.tarefas({
                url: {
                    base: '{{ route("tarefas.data") }}',
                    store: '{{ route("tarefas.store") }}'
                }
            });
        });
    </script>
</x-app-layout>