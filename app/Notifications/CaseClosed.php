<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class CaseClosed extends Notification implements ShouldQueue
{
    use Queueable;

    private $caseFile;

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

            $user = User::find($this->caseFile->doctor_id);
            $doctorData = $user->first_name.' '. $user->last_name;
            $message = 'Your Consultation With '.' '.$doctorData.' '.' Has Been Closed!';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/dashboard';
          
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
            $doctor = User::find($this->booking->partner_id);
            $doctorData = $doctor->first_name.' '. $doctor->last_name;
            return [
            'title' => 'Consultation Closed',
            'message' => 'Your Consultation With '.' '.$doctorData.' '.' Has Been Closed!',
            'url' => '/patients/dashboard',
            'time' => now(+1)->toDateTimeString()
       ];
        
        
    }
}
