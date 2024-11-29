@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Criar Tarefa</h1>

        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="start_time">Início</label>
                <input type="datetime-local" name="start_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end_time">Fim</label>
                <input type="datetime-local" name="end_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>
@endsection
