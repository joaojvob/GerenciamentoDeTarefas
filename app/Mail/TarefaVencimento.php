<?php

namespace App\Mail;

use App\Models\Tarefa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TarefaVencimento extends Mailable
{
    use Queueable, SerializesModels;

    public $tarefa;

    public function __construct(Tarefa $tarefa)
    {
        $this->tarefa = $tarefa;
    }

    public function build()
    {
        return $this->subject('Lembrete: Tarefa Próxima do Vencimento')->markdown('emails.tarefa_vencimento');
    }
}