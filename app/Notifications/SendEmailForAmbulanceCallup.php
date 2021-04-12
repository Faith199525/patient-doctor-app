<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class SendEmailForAmbulanceCallup extends Notification implements ShouldQueue
{
    use Queueable;

    private $callup;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($callup)
    {
        $this->callup = $callup;
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

   public function toMail($notifiable)
   {
       $Name = $notifiable->first_name.' '. $notifiable->last_name;

        if ($notifiable->hasRole('patient')){
            $ambulance = User::find($this->callup->ambulance_id);
            $user = $ambulance->first_name.' '. $ambulance->last_name;
            $message = 'Your Request For An Emergency Ambulance Service Has Been Accepted By '.$user;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/dashboard';
        }
        else {
            $patient = User::find($this->callup->user_id);
            $userp = $patient->first_name.' '. $patient->last_name;
            $message = 'Request For An Emergency Ambulance Service From '.$userp;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/ambulance/call-up-order';
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
            $ambulance = User::find($this->callup->ambulance_id);
            $user = $ambulance->first_name.' '. $ambulance->last_name;
            return [
            'title' => 'Request For Ambulance Service',
            'message' => 'Your Request For An Emergency Ambulance Service Has Been Accepted By '.$user,
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }else{

            $patient = User::find($this->callup->user_id);
            $userp = $patient->first_name.' '. $patient->last_name;
         return [
            'title' => 'Request For Ambulance Service',
            'message' => 'Request For An Emergency Ambulance Service From '.$userp,
            'url' => '/ambulance/call-up-order',
            'time' => now(+1)->toDateTimeString()
       ];
        }
          
    }
}
