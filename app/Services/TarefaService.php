<?php

namespace App\Services;

use App\Models\Tarefa;
use App\Repositories\TarefaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TarefaService
{
    protected $repository;

    public function __construct(TarefaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTarefas(): Collection
    {
        return $this->repository->getByUser(Auth::id(), Auth::user()->is_admin);
    }

    public function find($id): ?Tarefa
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Tarefa
    {
        if (!$this->canCreate()) {
            throw new \Exception('Você não tem permissão para criar tarefas.');
        }

        if ($this->repository->existsByDateAndUser(Auth::id(), $data['data_vencimento'] ?? null)) {
            throw new \Exception('Já existe uma tarefa neste horário.');
        }

        $data['user_id']    = Auth::id();
        $data['prioridade'] = $data['prioridade'] ?? 'media';
        $data['status']     = $data['status'] ?? 'pendente';
        $data['ordem']      = Tarefa::where('user_id', Auth::id())->max('ordem') + 1;

        return $this->repository->create($data);
    }

    public function update(Tarefa $tarefa, array $data): bool
    {
        if (!$this->canEdit($tarefa)) {
            throw new \Exception('Você não tem permissão para editar esta tarefa.');
        }

        if ($this->repository->existsByDateAndUser(Auth::id(), $data['data_vencimento'] ?? null, $tarefa->id)) {
            throw new \Exception('Já existe uma tarefa neste horário.');
        }

        return $this->repository->update($tarefa, $data);
    }

    public function delete(Tarefa $tarefa): bool
    {
        if (!$this->canDelete($tarefa)) {
            throw new \Exception('Você não tem permissão para excluir esta tarefa.');
        }
        return $this->repository->delete($tarefa);
    }

    public function getProductivityAnalysis($period = 'week'): array
    {
        $userId  = Auth::id();
        $isAdmin = Auth::user()->is_admin;

        $query = Tarefa::query();

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        if ($period === 'week') {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%U") as period, COUNT(*) as total, SUM(CASE WHEN status = "Concluida" THEN 1 ELSE 0 END) as concluidas')
                ->groupBy('period')
                ->orderBy('period')
                ->take(8)
                ->get();
        } else {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as total, SUM(CASE WHEN status = "Concluida" THEN 1 ELSE 0 END) as concluidas')
                ->groupBy('period')
                ->orderBy('period')
                ->take(12)
                ->get();
        }

        return $data->map(function ($item) {
            return [
                'period'     => $item->period,
                'total'      => $item->total,
                'concluidas' => $item->concluidas,
                'percentual' => $item->total > 0 ? ($item->concluidas / $item->total) * 100 : 0,
            ];
        })->toArray();
    }

    public function canCreate(): bool
    {
        return Auth::check();
    }

    public function canView(Tarefa $tarefa): bool
    {
        return Auth::user()->is_admin || $tarefa->user_id === Auth::id();
    }

    public function canEdit(Tarefa $tarefa): bool
    {
        return Auth::user()->is_admin || $tarefa->user_id === Auth::id();
    }

    public function canDelete(Tarefa $tarefa): bool
    {
        return Auth::user()->is_admin || $tarefa->user_id === Auth::id();
    }
}