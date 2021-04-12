<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SendEmailForPatientCaseFile extends Notification implements ShouldQueue
{
    use Queueable;

    public $caseFile;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($caseFile)
    {
        $this->caseFile = $caseFile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
       return ['database','mail','broadcast'];
    }

     public function toDatabase($notifiable)
    {
        return [
            'casefile'=> $this->caseFile
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage ([
            'casefile'=> $this->caseFile
        ]);
    }

    /**
 * Get the mail representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
   public function toMail($notifiable)
   {
    $url = url('https://drcallaway.ng/login');

    return (new MailMessage)
                ->greeting('Hello!')
                ->line('You have a notification!')
                ->action('View Notification', $url)
                ->line('Thank you for using our application!');
  }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
