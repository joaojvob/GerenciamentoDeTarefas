<?php

namespace App\Http\Controllers;

use App\Http\Requests\TarefaRequest;
use App\Services\TarefaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class TarefaController extends Controller
{
    protected $service;

    public function __construct(TarefaService $service)
    {
        $this->service = $service;
    }

    public function dataTable(): View
    {
        return view('dashboard');
    }

    public function data(): JsonResponse
    {
        $tarefas = $this->service->getTarefas();

        return datatables()->of($tarefas)
            ->addColumn('actions', fn ($tarefa) => view('tarefas.partials.actions', compact('tarefa'))->render())
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create(): View
    {
        return view('tarefas.create');
    }

    public function store(TarefaRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('dashboard')->with('success', 'Tarefa criada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id): JsonResponse
    {
        $tarefa = $this->service->find($id); 
        if (!$tarefa) {
            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }
        return response()->json($tarefa);
    }

    public function update(TarefaRequest $request, $id): JsonResponse
    {
        $tarefa = $this->service->find($id); 

        if (!$tarefa) {
            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        try {
            $this->service->update($tarefa, $request->validated());
            return response()->json(['success' => 'Tarefa atualizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id): JsonResponse
    {
        $tarefa = $this->service->find($id); 

        if (!$tarefa) {
            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        try {
            $this->service->delete($tarefa);
            return response()->json(['success' => 'Tarefa excluída com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function generatePdfReport(): \Illuminate\Http\Response
    {
        $tarefas = $this->service->getTarefas();
        $isAdmin = auth()->user()->is_admin;

        $data = [
            'tarefas'              => $tarefas,
            'is_admin'             => $isAdmin,
            'total_tarefas'        => $tarefas->count(),
            'tarefas_concluidas'   => $tarefas->where('status', 'Concluida')->count(),
            'percentual_concluido' => $tarefas->count() > 0 ? ($tarefas->where('status', 'Concluida')->count() / $tarefas->count()) * 100 : 0,
        ];

        $pdf = Pdf::loadView('tarefas.pdf.report', $data);
        return $pdf->download('relatorio_tarefas_' . now()->format('Ymd_His') . '.pdf');
    }

    public function productivityAnalysis(Request $request): JsonResponse
    {
        $period = $request->query('period', 'week');
        $data   = $this->service->getProductivityAnalysis($period);

        return response()->json($data);
    }
}