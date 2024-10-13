<?php

namespace App\Jobs;

use App\Models\ExportLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InsertExportLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $userId;
    protected $fileName;
    public function __construct(string $userId , string $fileName)
    {
        $this->userId = $userId;
        $this->fileName= $fileName;
        \Log::info('user_id =>'.$this->userId. "-----------".'fileName=>'.$this->fileName);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ExportLog::create([
            'user_id' => $this->userId,
            'file_name' => $this->fileName,
            'status' => 'pending',
        ]);
    }
}
