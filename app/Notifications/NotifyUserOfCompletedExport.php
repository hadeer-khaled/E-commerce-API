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

    protected $fileName ;
    /**
     * Create a new notification instance.
     */
    public function __construct( $fileName )
    {
        $this->fileName = $fileName ;
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
    public function toMail(object $notifiable): MailMessage
    {
        var_dump($this->fileName);
        
        $fileContent = Storage::disk('public')->get($this->fileName); 

    

        return (new MailMessage)
                ->subject('Your Export is Ready')
                ->line('The export you requested has been completed.')
                ->attachData($fileContent, $this->fileName, [
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                // ->action('Download File', url('storage/' . $this->fileName)) 
                ->line('Thank you for using our application!');
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
