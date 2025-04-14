<?php

namespace App\Jobs;

use App\Mail\TarefaVencimento;
use App\Models\Tarefa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarLembreteTarefa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tarefa;

    public function __construct(Tarefa $tarefa)
    {
        $this->tarefa = $tarefa;
    }

    public function handle()
    {
        Mail::to($this->tarefa->user->email)->send(new TarefaVencimento($this->tarefa));
    }
}