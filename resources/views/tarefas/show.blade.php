<div id="showTarefaModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Detalhes da Tarefa</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                <p id="showTitulo" class="mt-1 text-gray-900 dark:text-gray-100"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <div id="showDescricao" class="mt-1 text-gray-900 dark:text-gray-100 max-h-24 overflow-y-auto"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Vencimento</label>
                <p id="showDataVencimento" class="mt-1 text-gray-900 dark:text-gray-100"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridade</label>
                <p id="showPrioridade" class="mt-1 text-gray-900 dark:text-gray-100"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <p id="showStatus" class="mt-1 text-gray-900 dark:text-gray-100"></p>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="button" id="cancelShowTarefa" class="bg-gray-500 text-white py-2 px-4 rounded">Fechar</button>
        </div>
    </div>
</div>