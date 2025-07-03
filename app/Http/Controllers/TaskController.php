<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;
use Carbon\Carbon;
use App\Jobs\ExcluirTarefaJob;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource (com cache).
     */
    public function index()
    {
        $tasks = Cache::tags(['tasks'])->remember('task-list', now()->addSeconds(5), function () {

            return Task::where('finalizado', false)->get(); // só não finalizadas
        });

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage (com invalidação de cache).
     */
    public function store(Request $request)
    {
        $task = Task::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'finalizado' => $request->finalizado,
            'data_limite' => $request->data_limite,
        ]);

        // Invalida o cache da listagem
        Cache::tags(['tasks'])->flush();

        return response()->json($task, 201);
    }


    /**
     * Update the specified resource in storage (com invalidação de cache).
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        // Validação
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'finalizado' => 'boolean',
            'data_limite' => 'nullable|date_format:Y-m-d',
        ]);

        // Loga a data recebida
        \Log::info('Data recebida para atualização:', ['data_limite' => $request->data_limite]);

        // Normaliza a data_limite
        if (isset($validated['data_limite'])) {
            $validated['data_limite'] = Carbon::createFromFormat('Y-m-d', $validated['data_limite'])->startOfDay();
        }

        $task->update($validated);

        // Invalida o cache da listagem e da task individual
        Cache::tags(['tasks', "task-{$id}"])->flush();

        return response()->json($task);
    }

    public function toggleFinalizado(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $task->finalizado = !$task->finalizado;
        $retorno = $task->save();

        Cache::tags(['tasks', "task-{$id}"])->forget('task-list');
        Cache::tags(['tasks', "task-{$id}"])->forget("task-{$id}");

        if ($task->finalizado) {
            ExcluirTarefaJob::dispatch($task->id)->delay(now()->addSeconds(10));
        }

        return response()->json($task);
    }


    /**
     * Remove the specified resource from storage (com invalidação de cache).
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        // Invalida o cache da listagem e da task individual
        Cache::tags(['tasks', "task-{$id}"])->flush();

        return response()->json(['message' => 'Tarefa excluída com sucesso.']);
    }
}
