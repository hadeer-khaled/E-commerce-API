<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NotifyUserOfCompletedExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// class SendExportNotification implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $user;
//     protected $fileName;

//     /**
//      * Create a new job instance.
//      *
//      * @return void
//      */
//     public function __construct(User $user, $fileName)
//     {
//         $this->user = $user;
//         $this->fileName = $fileName;
//     }

//     /**
//      * Execute the job.
//      *
//      * @return void
//      */
//     public function handle()
//     {
//         $this->user->notify(new NotifyUserOfCompletedExport($this->fileName));
//     }
// }




class SendExportNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $fileNames;

    public function __construct(User $user, array $fileNames)
    {
        $this->user = $user;
        $this->fileNames = $fileNames;
        \Log::info( $this->fileNames);
    }

    public function handle()
    {
        $this->user->notify(new NotifyUserOfCompletedExport($this->fileNames));
    }
}
