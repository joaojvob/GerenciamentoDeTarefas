(function ($, window, document) {
    'use strict';

    var Tarefas = function () {
        var _this = this;
        this.options = {
            ajax: true,
            url: {
                base: window.tarefaRoutes.base,    
                store: window.tarefaRoutes.store,
                analise: window.tarefaRoutes.analise
            },
            datatables: {
                tarefas: null
            },
            chart: null
        };

        this.initDataTable = function () {
            var $table = $('#tarefasTable');

            _this.options.datatables.tarefas = $table.DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: _this.options.url.base,
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'titulo' },
                    { data: 'descricao' },
                    { data: 'data_vencimento' },
                    { data: 'prioridade' },
                    { data: 'status' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                                <button class="bg-green-500 text-white px-2 py-1 rounded item-edit" data-id="${data.id}">Editar</button>
                                <button class="bg-red-500 text-white px-2 py-1 rounded item-remove" data-id="${data.id}">Excluir</button>
                            `;
                        }
                    }
                ]
            });

            $table.on('click', '.item-edit', function () {
                let id = $(this).data('id');
                _this.editTarefa(id);
            });

            $table.on('click', '.item-remove', function () {
                let id = $(this).data('id');
                _this.removeTarefa(id);
            });
        };

        this.showCreateModal = function () {
            $('#createTarefaForm').trigger("reset");
            $('#createTarefaModal').removeClass('hidden');
        };

        this.showEditModal = function (data) {
            let $form = $('#editTarefaForm');
            let $modal = $('#editTarefaModal');

            $form.trigger("reset");
            $form.find('[name="titulo"]').val(data.titulo);
            $form.find('[name="descricao"]').val(data.descricao);
            $form.find('[name="data_vencimento"]').val(data.data_vencimento ? data.data_vencimento.slice(0, 16) : '');
            $form.find('[name="prioridade"]').val(data.prioridade);
            $form.find('[name="status"]').val(data.status);
            $form.data('id', data.id);
            $modal.removeClass('hidden');
        };

        this.editTarefa = function (id) {
            $.ajax({
                url: `/tarefas/${id}/edit`,
                type: 'GET',
                success: function (data) {
                    _this.showEditModal(data);
                },
                error: function (response) {
                    alert(response.responseJSON?.error || 'Erro ao buscar os detalhes da tarefa!');
                }
            });
        };

        this.removeTarefa = function (id) {
            if (!confirm('Tem certeza de que deseja excluir esta tarefa?')) return;
        
            $.ajax({
                url: `/tarefas/${id}`,
                type: 'DELETE',
                data: { _token: window.tarefaRoutes.csrfToken },  
                success: function (response) {
                    alert(response.success || 'Tarefa excluída com sucesso!');
                    _this.options.datatables.tarefas.ajax.reload();
                },
                error: function (response) {
                    alert(response.responseJSON?.error || 'Erro ao excluir a tarefa!');
                }
            });
        };

        this.initProductivityChart = function () {
            var ctx = document.getElementById('productivityChart').getContext('2d');

            function fetchData(period) {
                $.ajax({
                    url: _this.options.url.analise,
                    type: 'GET',
                    data: { period: period },
                    success: function (data) {
                        var labels = data.map(function (item) { return item.period; });
                        var totalData = data.map(function (item) { return item.total; });
                        var concluidasData = data.map(function (item) { return item.concluidas; });

                        if (_this.options.chart) {
                            _this.options.chart.destroy();
                        }

                        _this.options.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Tarefas Totais',
                                        data: totalData,
                                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    },
                                    {
                                        label: 'Tarefas Concluídas',
                                        data: concluidasData,
                                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                    },
                                ],
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                    },
                                },
                            },
                        });
                    },
                    error: function () {
                        alert('Erro ao carregar análise de produtividade!');
                    }
                });
            }

            fetchData('week');

            $('#periodSelect').on('change', function () {
                fetchData($(this).val());
            });
        };

        this.run = function (opts) {
            $.extend(true, _this.options, opts);

            _this.initDataTable();
            _this.initProductivityChart();

            $('#createTarefaButton').on('click', function () {
                _this.showCreateModal();
            });

            $('#cancelCreateTarefa').on('click', function () {
                $('#createTarefaForm').trigger("reset");
                $('#createTarefaModal').addClass('hidden');
            });

            $('#cancelEditTarefa').on('click', function () {
                $('#editTarefaModal').addClass('hidden');
            });

            $('#createTarefaForm').on('submit', function (e) {
                e.preventDefault();
                let data = $(this).serialize();

                $.ajax({
                    url: _this.options.url.store,
                    type: 'POST',
                    data: data, 
                    success: function () {
                        alert('Tarefa criada com sucesso!');
                        $('#createTarefaModal').addClass('hidden');
                        _this.options.datatables.tarefas.ajax.reload();
                        $('#createTarefaForm')[0].reset();
                    },
                    error: function (response) {
                        alert(response.responseJSON?.error || 'Erro ao criar a tarefa!');
                    }
                });
            });

            $('#editTarefaForm').on('submit', function (e) {
                e.preventDefault();
                let $form = $(this);
                let data = $form.serialize();
                let id = $form.data('id');

                $.ajax({
                    url: `/tarefas/${id}/update`,
                    type: 'PATCH',
                    data: data,
                    success: function (response) {
                        alert(response.success || 'Tarefa atualizada com sucesso!');
                        $('#editTarefaModal').addClass('hidden');
                        _this.options.datatables.tarefas.ajax.reload();
                    },
                    error: function (response) {
                        alert(response.responseJSON?.error || 'Erro ao atualizar a tarefa!');
                    }
                });
            });
        };
    };

    $.tarefas = function (opts) {
        var obj = $(window).data("app.tarefas");

        if (!obj) {
            obj = new Tarefas();
            obj.run(opts);
            $(window).data("app.tarefas", obj);
        }

        return obj;
    };

    $(document).ready(function() {
        $.tarefas();
    });

})(jQuery, window, document);