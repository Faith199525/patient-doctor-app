<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class KeyHealthIndicator extends Notification implements ShouldQueue
{
    use Queueable;

    private $subscriber;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
       return ['database','mail', 'broadcast'];
    }

    /**
 * Get the mail representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
   public function toMail($notifiable)
   {
       $Name = $notifiable->first_name.' '. $notifiable->last_name;

            $message ='Keep Track Of Your Key Health Indicators e.g Blood Pressure, Blood Sugar, Cholesterol e.t.c., Click Below To Proceed';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/medicalservices/annualmedicalscreening';
            
       
        return (new MailMessage)
                    ->greeting('Hello! '.$Name)
                    ->line($message)
                    ->action('View', $url)
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
            'title' => 'Key Health Inndicators',
            'message' => 'Keep Track Of Your Key Health Indicators e.g Blood Pressure, Blood Sugar, Cholesterol e.t.c., Click Below To Proceed',
            'url' => '/patients/medicalservices/annualmedicalscreening',
            'time' => now(+1)->toDateTimeString()
       ];
         
    }
}
