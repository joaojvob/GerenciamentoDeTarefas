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

    /**
     * Relacionamento: Tarefa pertence a um usuário.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
