<div id="createTarefaModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Adicionar Tarefa</h2>
        <form id="createTarefaForm" action="{{ route('tarefas.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" name="titulo" class="mt-1 block w-full rounded border-gray-300" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="descricao" class="mt-1 block w-full rounded border-gray-300" rows="3"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                <input type="datetime-local" name="data_vencimento" class="mt-1 block w-full rounded border-gray-300">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Prioridade</label>
                <select name="prioridade" class="mt-1 block w-full rounded border-gray-300">
                    <option value="Baixa">Baixa</option>
                    <option value="Média" selected>Média</option>
                    <option value="Alta">Alta</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded border-gray-300">
                    <option value="Pendente" selected>Pendente</option>
                    <option value="Em Andamento">Em Andamento</option>
                    <option value="Concluida">Concluída</option>
                </select>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancelCreateTarefa" class="bg-gray-500 text-white py-2 px-4 rounded mr-2">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Salvar</button>
            </div>
        </form>
    </div>
</div>