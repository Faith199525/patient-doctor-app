<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookedAppointment extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($appointment)
    {
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

        $action = 'View Booked Booking';
        $message2 = 'Kindly Proceed with service on the agreed date and time';
        
        return (new MailMessage)
                    ->greeting('Hello! '.$Name)
                    ->line($this->message())
                    ->action($action, $this->baseUrl().$this->pathUrl())
                    ->line($message2)
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
            'title' => 'Booked Appointment',
            'url' => $this->baseUrl().$this->pathUrl(),
            'message' => $this->message(),
            'time' => now(+1)->toDateTimeString()
       ];
    }

     /**
     * Get the base url for the frontend
     */
    protected function baseUrl()
    {
        return env('APP_DOMAIN','https://drcallaway.ng');
    }

     /**
     * 
     * Get the relative url path
     */

    protected function pathUrl()
    {
        return '/doctor/appointment';
    }

    /**
     * Get the notification message
     */

    protected function message()
    {
         $appointee = $this->appointment->appointmentable->patient; //Patient
         $appointeeName = $appointee->first_name.' '. $appointee->last_name;
         $message = 'Optical Service Appointment Booking Paid for by '.$appointeeName;

         return $message;
    }
}
