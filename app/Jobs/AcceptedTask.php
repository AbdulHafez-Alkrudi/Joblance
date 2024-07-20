<?php

namespace App\Jobs;

use App\Models\Users\AcceptedTasks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AcceptedTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $accepted_task;
    /**
     * Create a new job instance.
     */
    public function __construct(AcceptedTasks $acceptedTask)
    {
        $this->accepted_task = $acceptedTask;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->accepted_task->refresh();
        if ($this->accepted_task->duration == 1) {
            //
        }
        else {
            $this->accepted_task->update([
                'duration' => $this->accepted_task->duration - 1
            ]);
            dispatch(new AcceptedTask($this->accepted_task))->delay(now()->addDay());
        }
    }
}
