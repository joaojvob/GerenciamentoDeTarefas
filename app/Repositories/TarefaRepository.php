<?php

namespace App\Repositories;

use App\Models\Tarefa;
use Illuminate\Database\Eloquent\Collection;

class TarefaRepository
{
    public function getByUser($userId, $isAdmin): Collection
    {
        $query = Tarefa::query();
        
        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function find($id): ?Tarefa
    {
        return Tarefa::find($id);
    }

    public function create(array $data): Tarefa
    {
        return Tarefa::create($data);
    }

    public function update(Tarefa $tarefa, array $data): bool
    {
        return $tarefa->update($data);
    }

    public function delete(Tarefa $tarefa): bool
    {
        return $tarefa->delete();
    }

    public function existsByDateAndUser($userId, $dataVencimento, $excludeId = null): bool
    {
        $query = Tarefa::where('user_id', $userId)->where('data_vencimento', $dataVencimento);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}