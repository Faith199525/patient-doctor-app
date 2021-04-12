<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicalScreeningAppointment extends Notification implements ShouldQueue
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
       
        return (new MailMessage)
                    ->greeting('Hello! '.$Name)
                    ->line($this->message($notifiable))
                    ->action('View Booking', $this->baseUrl().$this->pathUrl($notifiable))
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
           'title' => 'Medical Screening Test Appointment',
           'url' => $this->baseUrl().$this->pathUrl($notifiable),
           'message' => $this->message($notifiable),
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

     protected function pathUrl($notifiable)
     {
        if ($notifiable->hasRole('patient')){
            return '/patients/appointments';
        } else {
            return '/diagnostic/appointments';
        }
     }

     /**
      * Get the notification message
    */

    protected function message($notifiable)
    {
        if ($notifiable->hasRole('patient')){

            $message = 'You booked a Medical Screening Test Apointment!';

            return $message;
        } else {
            $appointee = $this->appointment->appointmentable->patient;
            $appointeeName = $appointee->first_name. ' '. $appointee->last_name;
            $message =  $appointeeName.' has booked an appointment for a medical screening test';

            return $message;
        }
    }
}
