(function ($, window, document) {
    'use strict';

    var Tarefas = function () {
        var _this = this;
        this.options = {
            ajax: true,
            url: {
                base: '{{ route("tarefas.data") }}',
                store: '{{ route("tarefas.store") }}'
            },
            datatables: {
                tarefas: null
            }
        };

        this.initDataTable = function () {
            var $table = $('#tarefasTable');

            _this.options.datatables.tarefas = $table.DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: _this.options.url.base,
                    type: 'GET',
                    data: function(d) {
                        d.search = '';
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [
                    { data: 'titulo', name: 'titulo' },
                    { data: 'descricao', name: 'descricao' },
                    { data: 'data_vencimento', name: 'data_vencimento' },
                    { data: 'prioridade', name: 'prioridade' },
                    { data: 'status', name: 'status' },
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
                ],
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
            $form.find('[name="data_vencimento"]').val(data.data_vencimento?.slice(0, 16));
            $form.find('[name="prioridade"]').val(data.prioridade);
            $form.find('[name="status"]').val(data.status);
            $modal.removeClass('hidden');
        };

        this.editTarefa = function (id) {
            $.ajax({
                url: `/tarefas/${id}/edit`,
                type: 'GET',
                success: function (data) {
                    _this.showEditModal(data);
                },
                error: function () {
                    alert('Erro ao buscar os detalhes da tarefa!');
                }
            });
        };

        this.removeTarefa = function (id) {
            if (!confirm('Tem certeza de que deseja excluir esta tarefa?')) return;

            $.ajax({
                url: `/tarefas/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    alert('Tarefa excluída com sucesso!');
                    _this.options.datatables.tarefas.ajax.reload();
                },
                error: function () {
                    alert('Erro ao excluir a tarefa!');
                }
            });
        };

        this.run = function (opts) {
            $.extend(true, _this.options, opts);

            _this.initDataTable();

            $('#createTarefaButton').click(function () {
                _this.showCreateModal();
            });

            $('#cancelCreateTarefa').click(function () {
                $('#createTarefaForm').trigger("reset");
                $('#createTarefaModal').addClass('hidden');
            });

            $('#cancelEditTarefa').click(function () {
                $('#editTarefaModal').addClass('hidden');
            });

            $('#createTarefaForm').submit(function (e) {
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

            $('#editTarefaForm').submit(function (e) {
                e.preventDefault();
                let data = $(this).serialize();
                let id = $('#editTarefaModal').find('[data-id]').val() || $('.item-edit[data-id]').data('id');

                $.ajax({
                    url: `/tarefas/${id}/atualiza`,
                    type: 'PATCH',
                    data: data,
                    success: function () {
                        alert('Tarefa atualizada com sucesso!');
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

})(jQuery, window, document);