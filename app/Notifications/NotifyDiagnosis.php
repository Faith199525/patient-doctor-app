<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use App\Models\CaseFile;

class NotifyDiagnosis extends Notification implements ShouldQueue
{
    use Queueable;

    public $diagnosis;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($diagnosis)
    {
        $this->diagnosis = $diagnosis;
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
       $caseFile = CaseFile::find($this->diagnosis->case_file_id);

       if($this->diagnosis->status == 'ACTIVE'){
            $patient = User::find($caseFile->patient_id);
            $user = $patient->first_name.' '. $patient->last_name;

            if ($notifiable->hasRole('diagnostic')){
            $message = 'Medical Test Request From '.' '.$user;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/diagnostic/tests';
            }
            else {  
            $message = $user.' '.' Has Referred A Diagnostic Centre!';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       } else{

            if ($notifiable->hasRole('patient')){
            $message = 'Your Medicial Test Result has been uploaded!';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/prescriptions';
            }
            else {  
            $pat = User::find($caseFile->patient_id);
            $us = $pat->first_name.' '. $pat->last_name;
            $message = 'Medicial Test Result For '.' '.$us.' '.' Has Been Uploaded!';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       
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
        $caseFile = CaseFile::find($this->diagnosis->case_file_id);
        $patient = User::find($caseFile->patient_id);
        $user = $patient->first_name.' '. $patient->last_name;

        if($this->diagnosis->status == 'ACTIVE'){

            if($notifiable->hasRole('diagnostic')){
            return [
            'title' => 'Medical Diagnostic Test',
            'message' => 'Request To Run Medicial Test For '.' '.$user,
            'url' => '/diagnostic/tests',
            'time' => now(+1)->toDateTimeString()          
       ];
        }
         return [
            'title' => 'Medical Diagnostic Test',
            'message' => $user. 'Has Referred A Diagnostic Centre',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }else{

            if($notifiable->hasRole('patient')){
            return [
            'title' => 'Medical Diagnostic Test',
            'message' => 'Your Medicial Test Result Is Out',
            'url' => '/patients/prescriptions',
            'time' => now(+1)->toDateTimeString()
       ];
        }
            $patientD = User::find($caseFile->patient_id);
            $userD = $patientD->first_name.' '. $patientD->last_name;
            return [
            'title' => 'Medical Diagnostic Test',
            'message' => 'Medicial Test Result For '.' '.$userD. ' '.'Is Out',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }

    }
}
