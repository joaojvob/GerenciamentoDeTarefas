<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Botão para abrir o modal de criação -->
                    <button class="bg-blue-500 text-white py-2 px-4 rounded mb-4" id="createTaskButton">
                        Create Task
                    </button>

                    <!-- DataTables -->
                    <table id="tasksTable" class="table-auto w-full">
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('tasks.create', ['ajax' => true])

    <script>
        $(document).ready(function() {
            // Configuração do DataTables

            const table = $('#tasksTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('dashboard') }}',
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                    <a href="/tasks/${row.id}" class="btn btn-sm btn-info">View</a>
                                    <a href="/tasks/${row.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-task" data-id="${row.id}">Delete</button>
                                `;
                        }
                    },
                ],
            });

            $('#createTaskButton').on('click', function() {
                $('#createTaskModal').toggleClass('hidden');
            });

            $('#cancelCreateTask').on('click', function() {
                $('#createTaskModal').toggleClass('hidden');
            });

            $('#createTaskForm').on('submit', function(e) {
                e.preventDefault();
                const taskData = $(this).serialize(); // Captura todos os dados do formulário

                $.ajax({
                    url: '{{ route('tasks.store') }}',
                    type: 'POST',
                    data: taskData,
                    success: function(response) {
                        alert('Task created successfully!');
                        $('#createTaskModal').addClass('hidden');
                        table.ajax.reload();
                        $('#createTaskForm')[0].reset();
                    },
                    error: function(response) {
                        alert('Error creating task!');
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                const taskId = $(this).data('id');
                if (confirm('Are you sure you want to delete this task?')) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('Task deleted successfully!');
                            table.ajax.reload();
                        },
                        error: function(response) {
                            alert('Error deleting task!');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
