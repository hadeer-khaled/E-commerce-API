<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NotifyUserOfCompletedExport extends Notification
{
    use Queueable;

    protected $filePath ;
    protected $user ;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user , $filePath )
    {
        $this->user = $user ;
        $this->filePath = $filePath ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        var_dump($this->filePath);
        var_dump($this->user->id);
        return (new MailMessage)
                ->subject('Your Export is Ready')
                ->line('The export you requested has been completed.')
                // ->action('Download File', url('storage/' . $this->filePath)) 
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
