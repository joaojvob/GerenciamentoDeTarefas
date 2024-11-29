<form method="POST" action="{{ route('tasks.store') }}">
    @csrf
    <div class="form-group">
        <label for="title">Título</label>
        <input type="text" name="title" id="title" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Descrição</label>
        <textarea name="description" id="description" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="due_date">Data e Hora</label>
        <input type="datetime-local" name="due_date" id="due_date" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Salvar Tarefa</button>
</form>
