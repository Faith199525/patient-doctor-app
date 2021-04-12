<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $sub;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sub)
    {
        $this->sub = $sub;
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
       $expiry= $this->sub->end;
       $type= $this->sub->type;
       $plan= $this->sub->plan;

          if($this->sub->active == true){
            $message = 'Your Subscription Is Active. You Are On'.' '.$plan.' '.$type.' '.' Plan which will Expire on'.' '.$expiry;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/subscriptionplan';
          }

            else {
            $message = 'Your'.' '.$plan.' '.$type.' '.' Subscription Has Expired, Click Below To Subscribe';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/subscriptionplan';
            }
       
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
        $expiry= $this->sub->end;
        $type= $this->sub->type;
        $plan= $this->sub->plan;

        if($this->sub->active == true){
            return [
                'title' => 'Subscription',
                'message' => 'Your Subscription Is Active. You Are On'.' '.$plan.' '.$type.' '.' Plan which will Expire on'.' '.$expiry,
                'url' => '/patients/subscriptionplan',
                'time' => now(+1)->toDateTimeString()
           ];
        }
        return [
            'title' => 'Subscription',
            'message' => 'Your'.' '.$plan.' '.$type.' '.'Subscription Plan Has Expired, Click Below To Subscribe',
            'url' => '/patients/subscriptionplan',
            'time' => now(+1)->toDateTimeString()
       ];
         
    }
}
