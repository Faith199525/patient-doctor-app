<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public $casefile;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($casefile)
    {
        $this->casefile = $casefile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // No broadcast email notification:
        //     -Talk to a doctor
        //     -Request a Specialist

       // return ['database','broadcast','mail'];
        return ['database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {   
        $doctorName = $notifiable->first_name.' '. $notifiable->last_name;
        $patientName = $this->casefile->patient->first_name;
        $complaint = $this->casefile->initial_complain;

        return (new MailMessage)
                    ->greeting('Hello! '.$doctorName)
                    ->line($this->message())
                    ->line('Complaint: '.$complaint)
                    ->action('Attend to Patient', $this->baseUrl().$this->pathUrl())
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
            'title' => 'Service Request',
            'url' => $this->baseUrl().$this->pathUrl(),
            'message' => $this->message(),
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

     protected function pathUrl()
     {
        return '/doctor/patients';
     }

    /**
      * Get the notification message
    */

    protected function message()
    {
            $patient = $this->casefile->patient; //Patient
            $patientName = $patient->first_name.' '.$patient->last_name;
            $message = 'Service Request from '.$patientName;

            return $message;
    }
}
