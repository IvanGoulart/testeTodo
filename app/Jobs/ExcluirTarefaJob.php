<?php
namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ExcluirTarefaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function handle()
    {
        $task = Task::find($this->taskId);

        if (!$task || !$task->finalizado) {
            return;
        }

        $task->delete();

        // Limpa o cache para atualizar a lista na próxima requisição
        Cache::tags(['tasks'])->forget('task-list');
    }
}
