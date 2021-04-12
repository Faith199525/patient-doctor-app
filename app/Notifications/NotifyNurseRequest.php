<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NotifyNurseRequest extends Notification implements ShouldQueue
{
    use Queueable;

    private $booking;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
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

        if ($notifiable->hasRole('patient')){
            $nurse = User::find($this->booking->partner_id);
            $nurseData = $nurse->first_name.' '. $nurse->last_name;
            $message = 'Your Request For Nurse '.$nurseData.' Has Been Confirmed!';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/appointments';
        }
        else {
            $patient = User::find($this->booking->patient_id);
            $user = $patient->first_name.' '. $patient->last_name;
            $message = 'You Have A Booking From '.$user;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/nurse/booking';
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
        if($notifiable->hasRole('patient')){
            $nurse = User::find($this->booking->partner_id);
            $nurseData = $nurse->first_name.' '. $nurse->last_name;
            return [
            'title' => 'Request For Nurse',
            'message' => 'Your Request For Nurse '.$nurseData.' Has Been Confirmed!',
            'url' => '/patients/appointments',
            'time' => now(+1)->toDateTimeString()
       ];
        }
            $patient = User::find($this->booking->patient_id);
            $user = $patient->first_name.' '. $patient->last_name;
         return [
            'title' => 'Request For Nurse',
            'message' => 'You Have A Booking From '.$user,
            'url' => '/nurse/booking',
            'time' => now(+1)->toDateTimeString()
       ];
    }
}
