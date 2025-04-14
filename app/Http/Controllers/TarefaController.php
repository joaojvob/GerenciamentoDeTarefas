<?php

namespace App\Http\Controllers;

use App\Http\Requests\TarefaRequest;
use App\Models\Tarefa;
use App\Services\TarefaService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class TarefaController extends Controller
{
    protected $service;

    public function __construct(TarefaService $service)
    {
        $this->service = $service;
    }

    public function dataTable()
    {
        return view('dashboard');
    }

    public function data()
    {
        $tarefas = $this->service->getTarefas();

        $tarefas = $tarefas->map(function ($tarefa) {
            $tarefa->data_vencimento_formatada = $tarefa->data_vencimento_formatada;
            $tarefa->can_edit                  = $this->service->canEdit($tarefa);
            $tarefa->can_view                  = $this->service->canView($tarefa);
            $tarefa->can_delete                = $this->service->canDelete($tarefa);
            
            return $tarefa;
        });

        return response()->json(['data' => $tarefas]);
    }

    public function create()
    {
        if (!$this->service->canCreate()) {

            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para criar tarefas.');
        }
        
        return view('tarefas.create');
    }

    public function show($id)
    {
        $tarefa = Tarefa::findOrFail($id);

        if (!$this->service->canView($tarefa)) {

            return response()->json(['error' => 'Você não tem permissão para visualizar esta tarefa.'], 403);
        }

        return response()->json([
            'id'                        => $tarefa->id,
            'titulo'                    => $tarefa->titulo,
            'descricao'                 => $tarefa->descricao,
            'data_vencimento_formatada' => $tarefa->data_vencimento ? $tarefa->data_vencimento->format('d/m/Y H:i') : null,
            'prioridade'                => $tarefa->prioridade,
            'status'                    => $tarefa->status,
        ]);
    }

    public function store(TarefaRequest $request)
    {
        if (!$this->service->canCreate()) {

            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para criar tarefas.');
        }

        try {
            $this->service->create($request->validated());

            return redirect()->route('dashboard')->with('success', 'Tarefa criada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $tarefa = $this->service->find($id);

        if (!$tarefa) {

            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        if (!$this->service->canEdit($tarefa)) {

            return response()->json(['error' => 'Você não tem permissão para editar esta tarefa.'], 403);
        }

        return response()->json([
            'id'                        => $tarefa->id,
            'titulo'                    => $tarefa->titulo,
            'descricao'                 => $tarefa->descricao,
            'data_vencimento'           => $tarefa->data_vencimento ? $tarefa->data_vencimento->toDateTimeString() : null,
            'data_vencimento_formatada' => $tarefa->data_vencimento_formatada,
            'prioridade'                => $tarefa->prioridade,
            'status'                    => $tarefa->status
        ]);
    }

    public function update(TarefaRequest $request, $id)
    {
        $tarefa = $this->service->find($id); 

        if (!$tarefa) {

            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        if (!$this->service->canEdit($tarefa)) {

            return response()->json(['error' => 'Você não tem permissão para editar esta tarefa.'], 403);
        }

        try {
            $this->service->update($tarefa, $request->validated());

            return response()->json(['success' => 'Tarefa atualizada com sucesso!']);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        $tarefa = $this->service->find($id); 

        if (!$tarefa) {
            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        if (!$this->service->canDelete($tarefa)) {
            return response()->json(['error' => 'Você não tem permissão para excluir esta tarefa.'], 403);
        }

        try {
            $this->service->delete($tarefa);

            return response()->json(['success' => 'Tarefa excluída com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function generateReport($format = 'pdf')
    {
        $tarefas = $this->service->getTarefas();
        $isAdmin = auth()->user()->is_admin;

        $tarefas = $tarefas->map(function ($tarefa) {
            $tarefa->data_vencimento_formatada = $tarefa->data_vencimento_formatada ?? 'Não definido';

            return $tarefa;
        });

        $data = [
            'tarefas'              => $tarefas,
            'is_admin'             => $isAdmin,
            'total_tarefas'        => $tarefas->count(),
            'tarefas_concluidas'   => $tarefas->where('status', 'Concluida')->count(),
            'percentual_concluido' => $tarefas->count() > 0 ? ($tarefas->where('status', 'Concluida')->count() / $tarefas->count()) * 100 : 0,
        ];

        $timestamp = now()->format('Ymd_His');

        switch (strtolower($format)) {
            case 'pdf':
                $pdf = Pdf::loadView('tarefas.pdf.report', $data);

                return $pdf->download("relatorio_tarefas_{$timestamp}.pdf");

            case 'xlsx':
                $spreadsheet = new Spreadsheet();
                $sheet       = $spreadsheet->getActiveSheet();

                $headers = ['Título', 'Descrição', 'Data de Vencimento', 'Prioridade', 'Status'];

                if ($isAdmin) {
                    $headers[] = 'Usuário';
                }

                $col = 'A';
                
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}1", $header);
                    $col++;
                }

                // Dados
                $row = 2;
                foreach ($tarefas as $tarefa) {
                    $sheet->setCellValue("A{$row}", $tarefa->titulo ?? 'Sem título');
                    $sheet->setCellValue("B{$row}", $tarefa->descricao ?? 'Sem descrição');
                    $sheet->setCellValue("C{$row}", $tarefa->data_vencimento_formatada);
                    $sheet->setCellValue("D{$row}", $tarefa->prioridade ?? 'Não definido');
                    $sheet->setCellValue("E{$row}", $tarefa->status ?? 'Não definido');
                    if ($isAdmin) {
                        $sheet->setCellValue("F{$row}", $tarefa->user->name ?? 'Desconhecido');
                    }
                    $row++;
                }

                // Resumo
                $summaryRow = $row + 1;
                $sheet->setCellValue("A{$summaryRow}", 'Total de Tarefas');
                $sheet->setCellValue("B{$summaryRow}", $data['total_tarefas']);
                $sheet->setCellValue("A" . ($summaryRow + 1), 'Tarefas Concluídas');
                $sheet->setCellValue("B" . ($summaryRow + 1), $data['tarefas_concluidas']);
                $sheet->setCellValue("A" . ($summaryRow + 2), 'Percentual Concluído');
                $sheet->setCellValue("B" . ($summaryRow + 2), number_format($data['percentual_concluido'], 2) . '%');

                $writer   = new Xlsx($spreadsheet);
                $filename = "relatorio_tarefas_{$timestamp}.xlsx";

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment;filename=\"{$filename}\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');

                exit;

            case 'docx':
                $phpWord = new PhpWord();
                $section = $phpWord->addSection();
                $section->addTitle('Relatório de Tarefas', 1);
                $section->addText('Gerado em: ' . now()->format('d/m/Y H:i:s'));
                $section->addText('Usuário: ' . auth()->user()->name . ($isAdmin ? ' (Administrador)' : ''));

                // Resumo
                $section->addText('Total de Tarefas: ' . $data['total_tarefas']);
                $section->addText('Tarefas Concluídas: ' . $data['tarefas_concluidas']);
                $section->addText('Percentual Concluído: ' . number_format($data['percentual_concluido'], 2) . '%');
                $section->addTextBreak();

                // Tabela
                $table   = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
                $headers = ['Título', 'Descrição', 'Data de Vencimento', 'Prioridade', 'Status'];

                if ($isAdmin) {
                    $headers[] = 'Usuário';
                }

                $table->addRow();

                foreach ($headers as $header) {
                    $table->addCell(2000)->addText($header, ['bold' => true]);
                }

                foreach ($tarefas as $tarefa) {
                    $table->addRow();
                    $table->addCell(2000)->addText($tarefa->titulo ?? 'Sem título');
                    $table->addCell(4000)->addText($tarefa->descricao ?? 'Sem descrição');
                    $table->addCell(2000)->addText($tarefa->data_vencimento_formatada);
                    $table->addCell(1500)->addText($tarefa->prioridade ?? 'Não definido');
                    $table->addCell(1500)->addText($tarefa->status ?? 'Não definido');

                    if ($isAdmin) {
                        $table->addCell(2000)->addText($tarefa->user->name ?? 'Desconhecido');
                    }
                }

                $writer = IOFactory::createWriter($phpWord, 'Word2007');
                $filename = "relatorio_tarefas_{$timestamp}.docx";

                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header("Content-Disposition: attachment;filename=\"{$filename}\"");
                header('Cache-Control: max-age=0');

                $writer->save('php://output');

                exit;

            default:
                abort(400, 'Formato de relatório inválido.');
        }
    }

    public function productivityAnalysis(Request $request)
    {
        $period = $request->query('period', 'week');
        $data   = $this->service->getProductivityAnalysis($period);

        return response()->json($data);
    }
}