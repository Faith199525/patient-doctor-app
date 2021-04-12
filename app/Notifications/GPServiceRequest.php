<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GPServiceRequest extends Notification implements ShouldQueue
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

        if($notifiable->hasRole('doctor')){
            $action = 'View New Booking';
            $message2 = 'Your Response is needed to confirm the booking';

        } else {
            $action = 'View Booking';
            $message2 = 'You can proceed with the appointment on the specified date and time';
        }
       
      
       
        return (new MailMessage)
                    ->greeting('Hello! '.$Name)
                    ->line($this->message($notifiable))
                    ->action($action, $this->baseUrl().$this->pathUrl($notifiable))
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
            'title' => 'General Practitioner Service Request',
            'url' => $this->baseUrl().$this->pathUrl($notifiable),
            'message' => $this->message($notifiable),
            'time' => now(+1)->toDateTimeString(),
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

     protected function pathUrl($notifiable)
     {
        if ($notifiable->hasRole('doctor')){
            return '/doctor/appointment';
        } else {
            return '/patients/appointments';
        }
     }

    /**
      * Get the notification message
    */

    protected function message($notifiable)
    {
        if ($notifiable->hasRole('doctor')){

            $appointee = $this->appointment->appointmentable->patient; //Patient
            $appointeeName = $appointee->first_name.' '. $appointee->last_name;
            $message = 'General Practitioner Service Appointment Booking from '.$appointeeName;

            return $message;
        } else {

            $appointer = $this->appointment->appointmentable->serviceCenter; //Doctor
            $appointerName = $appointer->first_name.' '. $appointer->last_name;
            $message = 'Your General Practitioner Service Appointment Booking has been confirmed by '.$appointerName;

            return $message;
        }
    }
}
