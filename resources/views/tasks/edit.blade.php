<div id="editTaskModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Editar Tarefa</h2>
        <form id="editTaskForm">
            <input type="hidden" id="editTaskId" name="task_id" value="">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" id="title" name="title" class="mt-1 block w-full rounded border-gray-300"
                    required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea id="description" name="description" class="mt-1 block w-full rounded border-gray-300" rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Inicio</label>
                <input type="datetime-local" id="start_time" name="start_time"
                    class="mt-1 block w-full rounded border-gray-300">
            </div>

            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700">Fim</label>
                <input type="datetime-local" id="end_time" name="end_time"
                    class="mt-1 block w-full rounded border-gray-300">
            </div>

            <div class="flex justify-end">
                <button type="button" id="cancelEditTask" class="bg-gray-500 text-white py-2 px-4 rounded mr-2">
                    Cancel
                </button>

                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">
                    Salvar Tarefa
                </button>
            </div>
        </form>
    </div>
</div>
