<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NutritionistServiceRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public $nutritionistService;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($nutritionistService)
    {
        $this->nutritionistService = $nutritionistService;
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
        $nutritionist = $notifiable->first_name.' '.$notifiable->last_name;
        $patientName = $this->nutritionistService->patient->first_name;
        $complaint = $this->nutritionistService->initial_complain;

        return (new MailMessage)
                    ->greeting('Hello '.$nutritionist.',')
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
            'title' => 'Nutritionist Service Request',
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
        return '/nutritionist/patients';
     }

    /**
      * Get the notification message
    */

    protected function message()
    {
            $patient = $this->nutritionistService->patient; //Patient
            $patientName = $patient->first_name.' '.$patient->last_name;
            $message = 'Nutritionist Service Request from '.$patientName;

            return $message;
    }
}
