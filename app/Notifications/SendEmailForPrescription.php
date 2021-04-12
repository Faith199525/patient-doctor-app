<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use App\Models\CaseFile;

class SendEmailForPrescription extends Notification implements ShouldQueue
{
    use Queueable;

    public $prescription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($prescription)
    {
        $this->prescription = $prescription;
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
       $caseFile = CaseFile::find($this->prescription->case_file_id);
       $cid= (int)$this->prescription->case_file_id; 

       if($this->prescription->status == 'PENDING'){
            $patient = User::find($caseFile->patient_id);
            $user = $patient->first_name.' '. $patient->last_name;

            if ($notifiable->hasRole('pharmacy')){
            $message = 'You Have Drugs Prescription Order For' .' '.$user;
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/pharmacy/prescription';
            }
            else {  
            $message = $user.' '. 'Has Referred A Pharmacy To Get Drugs Prescription';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       } 
       if($this->prescription->status == 'ACCEPTED'){

            if ($notifiable->hasRole('patient')){
            $message = 'Pharmacy Has Accepted Your Referral, And Costed The Drugs';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/prescriptions';
            }
            else {  
            $pat = User::find($caseFile->patient_id);
            $us = $pat->first_name.' '. $pat->last_name;
            $message = $us.' '. 'Drugs Order Referral For '. ' ' .$us. ' '.'Has Been Accepted By Pharmacy';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       }
         if($this->prescription->status == 'REJECTED'){

            if ($notifiable->hasRole('patient')){
            $message = 'Pharmacy Rejected Your Referral, Click Below To Refer Another Pharmacy';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/patients/chats/'.$cid;
            }
            else {  
            $pa = User::find($caseFile->patient_id);
            $usp = $pa->first_name.' '. $pa->last_name;
            $message ='Drugs Order Referral For '.' '.$usp.''. 'Has Been Rejected By Pharmacy, Patient Will Refer Another Pharmacy';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       } 
         if($this->prescription->status == 'APPROVED'){
            $pac = User::find($caseFile->patient_id);
            $ud = $pac->first_name.' '. $pac->last_name;

            if ($notifiable->hasRole('pharmacy')){
            $message = $ud.' '. 'Has Accepted Drugs Prescription Cost';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/pharmacy/prescription';
            }
            else {  
            $message =$ud.' '. 'Has Accepted Drugs Prescription Cost';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/doctor/dashboard';
        }
       }
       if($this->prescription->status == 'DECLINED'){
            $pact = User::find($caseFile->patient_id);
            $udp = $pact->first_name.' '. $pact->last_name;

            if ($notifiable->hasRole('pharmacy')){
            $message = $udp.' '. 'Declined Drugs Prescription Cost';
            $url = env('APP_DOMAIN','https://drcallaway.ng').'/pharmacy/dashboard';
            }
            else {  
            $message =$udp.' '. 'Declined Drugs Prescription Cost';
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
        $caseFile = CaseFile::find($this->prescription->case_file_id);
        $cid= (int)$this->prescription->case_file_id;

        if($this->prescription->status == 'PENDING'){
            $patientD = User::find($caseFile->patient_id);
            $userD = $patientD->first_name.' '. $patientD->last_name;

            if($notifiable->hasRole('pharmacy')){
            return [
            'title' => 'Drugs Prescription',
            'message' => 'You Have Drugs Prescription Order For '.' '.$userD,
            'url' => '/pharmacy/prescription',
            'time' => now(+1)->toDateTimeString()
            
       ];
        }
         return [
            'title' => 'Drugs Prescription',
            'message' => $userD. ' '.'Has Referred A Pharmacy To Get Drugs Prescription',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }
        else if($this->prescription->status == 'ACCEPTED'){
           if($notifiable->hasRole('patient')){
            return [
            'title' => 'Drugs Prescription',
            'message' => 'Pharmacy Has Accepted Your Referral, And Costed The Drugs',
            'url' => '/patients/prescriptions',
            'time' => now(+1)->toDateTimeString()
       ];
        }   
            $pat = User::find($caseFile->patient_id);
            $userp = $pat->first_name.' '. $pat->last_name;
            return [
            'title' => 'Drugs Prescription',
            'message' => 'Drugs Order Referral For '.' '.$userp.' '. 'Has Been Accepted By Pharmacy',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }
        else if($this->prescription->status == 'REJECTED'){
           if($notifiable->hasRole('patient')){
            return [
            'title' => 'Drugs Prescription',
            'message' => 'Pharmacy Rejected Your Referral, Click Below To Refer Another Pharmacy',
            'url' => '/patients/chats/'.$cid,
            'time' => now(+1)->toDateTimeString()
       ];
        }   
            $caseuser = User::find($caseFile->patient_id);
            $usercase = $caseuser->first_name.' '. $caseuser->last_name;
            return [
            'title' => 'Drugs Prescription',
            'message' => 'Drugs Order Referral For '.' '.$usercase. ' '.'Has Been Rejected By Pharmacy, Patient Will Refer Another Pharmacy',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }
        else if($this->prescription->status == 'APPROVED'){
            $pD = User::find($caseFile->patient_id);
            $uD = $pD->first_name.' '. $pD->last_name;

           if($notifiable->hasRole('pharmacy')){
            return [
            'title' => 'Drugs Prescription',
            'message' => $uD.' '. 'Has Accepted Drugs Prescription Cost',
            'url' => '/pharmacy/prescription',
            'time' => now(+1)->toDateTimeString()
       ];
        }   
            return [
            'title' => 'Drugs Prescription',
            'message' => $uD.' '. 'Has Accepted Drugs Prescription Cost',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }
        else{
            $paD = User::find($caseFile->patient_id);
            $usD = $paD->first_name.' '. $paD->last_name;

           if($notifiable->hasRole('pharmacy')){
            return [
            'title' => 'Drugs Prescription',
            'message' => $usD.' '. 'Declined Drugs Prescription Cost',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }   
            return [
            'title' => 'Drugs Prescription',
            'message' => $usD.' '. 'Declined Drugs Prescription Cost',
            'url' => '#',
            'time' => now(+1)->toDateTimeString()
       ];
        }

    }

}
