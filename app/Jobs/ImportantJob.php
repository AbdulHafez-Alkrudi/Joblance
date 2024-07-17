<?php

namespace App\Jobs;

use App\Models\Users\Company\ImportantJobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $importantJob;
    public function __construct(ImportantJobs $importantJob)
    {
        $this->importantJob = $importantJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->importantJob->delete();
    }
}
