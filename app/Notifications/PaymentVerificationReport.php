<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerificationReport extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $report;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payment,$report)
    {
        $this->payment = $payment;
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast','mail'];
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
       return (new MailMessage)
                    ->greeting('Hello! '.$Name)
                    ->line($this->message())
                    ->action('Go to app', $this->baseUrl().$this->pathUrl())
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
            'title' => 'Payment Verification Report',
            //'url' => $this->baseUrl().$this->pathUrl(),
            'url' => $this->pathUrl(),
            'message' => '',
            'time' => now(+1)->toDateTimeString(),
       ];
    }

     /**
     * 
     * Get the base url for the frontend
     * 
     */
    protected function baseUrl()
    {
        return env('APP_DOMAIN','https://drcallaway.ng');
    }

    /**
     * 
     * Get the relative url path
     * 
     */

     protected function pathUrl()
     {
         return '#';

     }

      /**
      * Get the notification message
    */

    protected function message()
    {
        $message = '';

        return $message;
    }
}
