<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Tarefa extends Model
{
    use HasFactory;

    protected $table = 'tarefas';

    protected $fillable = [
        'titulo',
        'descricao',
        'prioridade',
        'status',
        'data_vencimento',
        'ordem',
        'user_id',
    ];

    protected $casts = [
        'data_vencimento' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private function authorizeTarefa(Tarefa $tarefa)
    {
        if (!auth()->user()->is_admin && $tarefa->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para esta ação.');
        }
    }
}
