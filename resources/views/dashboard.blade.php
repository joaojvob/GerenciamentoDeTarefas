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
                    <button class="bg-blue-500 text-white py-2 px-4 rounded mb-4" id="createTaskButton">
                        Cria Tarefa
                    </button>

                    <table id="tasksTable" class="table-auto w-full">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Inicio</th>
                                <th>Fim</th>
                                <th> </th>
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
    @include('tasks.create', ['ajax' => true])
    @include('tasks.edit', ['ajax' => true])

    <script>
        $(document).ready(function() {
            const table = $('#tasksTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('tasks.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.search = '';
                    },
                    dataSrc: function(json) {
                        console.log(json);
                        var data = json.data;
                        return data;
                    }
                },
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <button class="bg-green-500 text-white px-2 py-1 rounded edit-task" data-id="${row.id}">
                        Editar
                    </button>
                    <button class="bg-red-500 text-white px-2 py-1 rounded delete-task" data-id="${row.id}">
                        Excluir
                    </button>
                `;
                        }
                    }
                ]
            });

            $('#createTaskButton').on('click', function() {
                $('#createTaskForm')[0].reset();
                $('#createTaskModal').toggleClass('hidden');
            });

            $('#cancelCreateTask').on('click', function() {
                $('#createTaskForm')[0].reset();
                $('#createTaskModal').toggleClass('hidden');
            });

            $('#createTaskForm').on('submit', function(e) {
                e.preventDefault();
                const taskData = $(this).serialize();

                $.ajax({
                    url: '{{ route('tasks.store') }}',
                    type: 'POST',
                    data: taskData,
                    success: function(response) {
                        alert('Tarefa criada com sucesso!');
                        $('#createTaskModal').addClass('hidden');
                        table.ajax.reload();
                        $('#createTaskForm')[0].reset();
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.error) {
                            alert(response.responseJSON.error);
                        } else {
                            alert('Erro ao criar a tarefa!');
                        }
                    }
                });
            });

            $(document).on('click', '.edit-task', function() {
                const taskId = $(this).data('id');

                $.ajax({
                    url: `/tasks/${taskId}/edit`,
                    type: 'GET',
                    success: function(task) {
                        $('#editTaskModal #title').val(task.title);
                        $('#editTaskModal #description').val(task.description);
                        $('#editTaskModal #start_time').val(task.start_time);
                        $('#editTaskModal #end_time').val(task.end_time);
                        $('#editTaskModal').removeClass('hidden');
                    },
                    error: function(response) {
                        alert('Erro ao buscar os detalhes da tarefa!');
                    }
                });

                $('#editTaskForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    const taskData = $(this).serialize();

                    $.ajax({
                        url: `/tasks/${taskId}/atualiza`,
                        type: 'PATCH',
                        data: taskData,
                        success: function(response) {
                            alert('Tarefa atualizada com sucesso!');
                            $('#editTaskModal').addClass('hidden');
                            table.ajax.reload();
                            $('#editTaskForm')[0].reset();
                        },
                        error: function(response) {
                            if (response.responseJSON && response.responseJSON.error) {
                                alert(response.responseJSON.error);
                            } else {
                                alert('Erro ao atualizar a tarefa!');
                            }
                        }
                    });

                });
            });

            $('#cancelEditTask').on('click', function() {
                $('#editTaskModal').toggleClass('hidden');
            });

            $(document).on('click', '.delete-task', function() {
                const taskId = $(this).data('id');
                if (confirm('Tem certeza de que deseja excluir esta tarefa?')) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('Tarefa excluída com sucesso!');
                            table.ajax.reload();
                        },
                        error: function(response) {
                            alert('Erro ao excluir a tarefa!');
                        }
                    });
                }
            });

        });
    </script>

</x-app-layout>
