<?php

namespace Tests\Feature;

use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_user_can_create_tarefa()
    {
        $response = $this->actingAs($this->user)->post(route('tarefas.store'), [
            'titulo' => 'Teste Tarefa',
            'descricao' => 'Descrição',
            'prioridade' => 'Média',
            'status' => 'Pendente',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('tarefas', [
            'titulo' => 'Teste Tarefa',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_admin_can_see_all_tarefas()
    {
        Tarefa::factory()->create(['user_id' => $this->user->id]);
        Tarefa::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('tarefas.data'));

        $response->assertStatus(200);
        $this->assertCount(2, $response->json()['data']);
    }

    public function test_non_admin_can_only_see_own_tarefas()
    {
        Tarefa::factory()->create(['user_id' => $this->user->id]);
        Tarefa::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user)->get(route('tarefas.data'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']);
    }

    public function test_generate_pdf_report()
    {
        Tarefa::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('tarefas.relatorio.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}