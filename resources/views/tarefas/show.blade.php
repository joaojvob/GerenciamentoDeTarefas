<div id="showTarefaModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden min-h-screen transition-opacity duration-300">
    <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-lg sm:max-w-sm transform transition-all duration-300 scale-95">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Detalhes da Tarefa</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                <p id="showTitulo" class="mt-1 text-gray-900 dark:text-gray-100"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <div id="showDescricao" class="mt-1 text-gray-900 max-h-40 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-sm leading-6 overflow-wrap-break-word font-bold"></div>
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
            <button type="button" id="cancelShowTarefa" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white py-2 px-4 rounded-lg hover:from-gray-600 hover:to-gray-700 hover:shadow-md transition-all duration-200">Fechar</button>
        </div>
    </div>
</div>