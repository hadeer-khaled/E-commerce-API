<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class NotifyUserOfCompletedExport extends Notification implements ShouldQueue
{
    use Queueable;

    // protected $fileName ;
    /**
     * Create a new notification instance.
     */
    // public function __construct( $fileName )
    // {
    //     $this->fileName = $fileName ;
    // }

    protected $fileNames ;

    public function __construct(array $fileNames)
    {
        $this->fileNames = $fileNames;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        \Log::info('via method called for NotifyUserOfCompletedExport');
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     var_dump($this->fileName);

    //     $fileContent = Storage::disk('public')->get($this->fileName);



    //     return (new MailMessage)
    //             ->subject('Your Export is Ready')
    //             ->line('The export you requested has been completed.')
    //             ->attachData($fileContent, $this->fileName, [
    //                 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //             ])
    //             // ->action('Download File', url('storage/' . $this->fileName))
    //             ->line('Thank you for using our application!');
    // }


    public function toMail($notifiable)
    {
        $email = (new MailMessage)
            ->subject('Your Export Files are Ready')
            ->line('The export you requested has been completed.');

        foreach ($this->fileNames as $fileName) {
            \Log::info("Checking file at: " . public_path("storage/" . $fileName));


            if (Storage::disk('public')->exists($fileName)) {
                $fileContent = Storage::disk('public')->get($fileName);
                if ($fileContent) {
                    $email->attachData($fileContent, $fileName, [
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
                } else {
                    \Log::error("File content is null for: " . $fileName);
                }
            } else {
                \Log::error("File does not exist: " . $fileName);
            }
        }

        $email->line('Thank you for using our application!');
        return $email;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
    // public function toUser()
    // {
    //     return  $this->user;
    // }
}
