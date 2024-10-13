<?php

namespace App\Console;

use App\Notifications\NotifyUserOfCompletedExport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendExportNotification;
use App\Models\ExportLog;
use App\Models\User;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
//        $schedule->call(function () {
//            \Log::info("after 5 seconds");
//        })->everyFiveSeconds();;

        $schedule->call(function () {
            \Log::info("Checking for pending export logs.");
            $exportLogs = ExportLog::where('status', 'pending')->get();

            if ($exportLogs->isEmpty()) {
                \Log::info("No pending export logs found.");
            }

            foreach ($exportLogs as $log) {
                $user = User::find($log->user_id);
                \Log::info("Found export log for user ID: {$log->user_id}");

                if ($user) {
                    $user->notify(new NotifyUserOfCompletedExport($log->file_name));
                    $log->update(['status' => 'sent']);
                    \Log::info("Email sent and log updated for user ID: {$log->user_id}");
                } else {
                    \Log::warning("User with ID {$log->user_id} not found.");
                }
            }
        })->everyThirtySeconds();



//        $schedule->job(new SendExportNotification() )->everyMinute();


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
