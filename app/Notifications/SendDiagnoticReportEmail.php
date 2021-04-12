<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendDiagnoticReportEmail extends Notification implements ShouldQueue
{
    use Queueable;
    
    private $report;

    private $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($report, $appointment)
    {
        $this->report = $report;
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'report'=> $this->report,
            'appointment'=> $this->appointment,
            'report' => $notifiable
        ];
    }

        /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
   public function toMail($notifiable)
   {
    $url = url('/notification');

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
