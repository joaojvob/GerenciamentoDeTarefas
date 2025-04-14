(function ($, window, document) {
    'use strict';

    var Tarefas = function () {
        var _this = this;
        this.options = {
            ajax: true,
            url: {
                base: window.tarefaRoutes.base,
                store: window.tarefaRoutes.store,
                analise: window.tarefaRoutes.analise,
                relatorio: window.tarefaRoutes.relatorio
            },
            datatables: {
                tarefas: null
            },
            chart: null
        };

        this.makeFieldSelect2Simple = function (el, data, multiple) {
            data = data || [];
            multiple = multiple || false;

            var options = {
                allowClear: true,
                placeholder: "Selecione",
                minimumResultsForSearch: data.length < 50 ? -1 : 0,
                data: data
            };

            if (multiple) {
                options['multiple'] = true;
            }

            return el.select2(options);
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
                    { data: 'ordem' },
                    {
                        data: 'titulo',
                        render: function (data, type, row) {
                            return `<span data-fulltext="${data}">${data}</span>`;
                        }
                    },
                    {
                        data: 'data_vencimento_formatada',
                        render: function (data) {
                            return data || 'Não definido';
                        }
                    },
                    { data: 'prioridade' },
                    {
                        data: 'status',
                        render: function (data, type, row) {
                            return `<span data-fulltext="${data}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                                <button class="bg-blue-500 text-white px-2 py-1 rounded item-show" data-id="${data.id}">Visualizar</button>
                                <button class="bg-green-500 text-white px-2 py-1 rounded item-edit" data-id="${data.id}">Editar</button>
                                <button class="bg-red-500 text-white px-2 py-1 rounded item-remove" data-id="${data.id}">Excluir</button>
                            `;
                        }
                    }
                ]
            });

            $table.on('click', '.item-show', function () {
                var id = $(this).data('id');
                _this.showTarefa(id);
            });

            $table.on('click', '.item-edit', function () {
                var id = $(this).data('id');
                _this.editTarefa(id);
            });

            $table.on('click', '.item-remove', function () {
                var id = $(this).data('id');
                _this.removeTarefa(id);
            });
        };

        this.showCreateModal = function () {
            _this.showTarefaModal(null, false);
        };

        this.showEditModal = function (data) {
            _this.showTarefaModal(data, true);
        };

        this.editTarefa = function (id) {
            $.ajax({
                url: `/tarefas/${id}/edit`,
                type: 'GET',
                success: function (data) {
                    _this.showEditModal(data);
                },
                error: function (response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.responseJSON?.error || 'Erro ao buscar os detalhes da tarefa!'
                    });
                }
            });
        };

        this.removeTarefa = function (id) {
            Swal.fire({
                title: 'Confirmar Exclusão',
                text: 'Tem certeza que deseja excluir esta tarefa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/tarefas/${id}`,
                        type: 'DELETE',
                        data: { _token: window.tarefaRoutes.csrfToken },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tarefa Excluída!',
                                text: response.success || 'A tarefa foi removida com sucesso.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            _this.options.datatables.tarefas.ajax.reload();
                        },
                        error: function (response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.responseJSON?.error || 'Não foi possível excluir a tarefa.'
                            });
                        }
                    });
                }
            });
        };

        this.showTarefa = function (id) {
            $.ajax({
                url: `/tarefas/${id}`,
                type: 'GET',
                success: function (data) {
                    if (!data || typeof data !== 'object') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Resposta inválida do servidor.'
                        });
                        return;
                    }

                    $('#showTitulo').text(data.titulo || 'Sem título');
                    $('#showDescricao').text(data.descricao || 'Sem descrição');
                    $('#showDataVencimento').text(data.data_vencimento_formatada || 'Não definido');
                    $('#showPrioridade').text(data.prioridade || 'Não definido');
                    $('#showStatus').text(data.status || 'Não definido');
                    $('#showTarefaModal').removeClass('hidden');
                },
                error: function (response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.responseJSON?.error || 'Não foi possível carregar os detalhes da tarefa.'
                    });
                }
            });
        };

        this.showTarefaModal = function (data, isEdit) {
            let $form = $('#tarefaForm');
            let $modal = $('#tarefaModal');
            let $title = $('#tarefaModalTitle');
            let $method = $form.find('[name="_method"]');
            let $submit = $('#submitTarefa');

            $form.trigger("reset");
            $method.val(isEdit ? 'PATCH' : 'POST');
            $title.text(isEdit ? 'Editar Tarefa' : 'Adicionar Tarefa');
            $submit.text(isEdit ? 'Salvar Tarefa' : 'Salvar');

            if (isEdit && data) {
                $form.find('[name="titulo"]').val(data.titulo);
                $form.find('[name="descricao"]').val(data.descricao);
                $form.find('[name="data_vencimento"]').val(data.data_vencimento ?
                    new Date(data.data_vencimento).toISOString().slice(0, 16) : '');
                $form.find('[name="prioridade"]').val(data.prioridade);
                $form.find('[name="status"]').val(data.status);
                $form.data('id', data.id);
            }

            _this.makeFieldSelect2Simple($form.find('select[name="prioridade"]'), [
                { id: 'Baixa', text: 'Baixa' },
                { id: 'Média', text: 'Média' },
                { id: 'Alta', text: 'Alta' }
            ]);

            _this.makeFieldSelect2Simple($form.find('select[name="status"]'), [
                { id: 'Pendente', text: 'Pendente' },
                { id: 'Em Andamento', text: 'Em Andamento' },
                { id: 'Concluida', text: 'Concluída' },
                { id: 'Cancelada', text: 'Cancelada' }
            ]);

            $modal.removeClass('hidden');
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao carregar análise de produtividade!'
                        });
                    }
                });
            }

            fetchData('week');

            $('#periodSelect').on('change', function () {
                fetchData($(this).val());
            });
        };

        this.exportReport = function () {
            var format = $('#reportFormat').val();
            var url = _this.options.url.relatorio.replace(':format', format);
            window.location.href = url;
        };

        this.fetchProductivityData = function (period) {
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

                    var ctx = document.getElementById('productivityChart').getContext('2d');
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao carregar análise de produtividade!'
                    });
                }
            });
        };

        this.initProductivityChart = function () {
            _this.fetchProductivityData('week');

            $('#periodSelect').on('change', function () {
                _this.fetchProductivityData($(this).val());
            });
        };

        this.run = function (opts) {
            $.extend(true, _this.options, opts);

            _this.initDataTable();
            _this.initProductivityChart();

            setInterval(function () {
                _this.options.datatables.tarefas.ajax.reload(null, false);
            }, 2 * 1000);

            setInterval(function () {
                var period = $('#periodSelect').val() || 'week';
                _this.fetchProductivityData(period);
            }, 2 * 1000);

            $('#createTarefaButton').on('click', function () {
                _this.showCreateModal();
            });

            $('#cancelTarefa').on('click', function () {
                $('#tarefaForm').trigger("reset");
                $('#tarefaModal').addClass('hidden');
            });

            $('#cancelShowTarefa').on('click', function () {
                $('#showTarefaModal').addClass('hidden').removeClass('opacity-100');
            });

            $('#reloadTarefas').on('click', function () {
                _this.options.datatables.tarefas.ajax.reload(null, false);
            });

            $('#exportReport').on('click', function () {
                _this.exportReport();
            });

            $('#tarefaForm').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var data = $form.serialize();
                var isEdit = $form.find('[name="_method"]').val() === 'PATCH';
                var url = isEdit ? `/tarefas/${$form.data('id')}/update` : _this.options.url.store;
                var method = isEdit ? 'PATCH' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: isEdit ? 'Tarefa Atualizada!' : 'Tarefa Criada!',
                            text: response.success || (isEdit ? 'A tarefa foi atualizada com sucesso.' : 'A tarefa foi adicionada com sucesso.'),
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#tarefaModal').addClass('hidden');
                        _this.options.datatables.tarefas.ajax.reload(null, false);
                        $form[0].reset();
                    },
                    error: function (response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: response.responseJSON?.error || (isEdit ? 'Não foi possível atualizar a tarefa.' : 'Não foi possível criar a tarefa.')
                        });
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

    $(document).ready(function () {
        $.tarefas();
    });

})(jQuery, window, document);