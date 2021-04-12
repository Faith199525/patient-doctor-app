<?php

namespace App\Listeners;

use App\Events\PaymentMade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\PaymentVerificationReport;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class VerifyPayment implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PaymentMade  $event
     * @return void
     */
    public function handle(PaymentMade $event)
    {
        $this->verify($event->payment);
    }


    /**
     * Handle a job failure.
     *
     * @param  \App\Events\PaymentMade  $event
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(PaymentMade $event, $exception)
    {
        //Notify Admins
        
        Notification::send($this->getAdmins(), new PaymentVerificationReport($event->payment,$exception));
        
    }

     /**
     * Verify Payment 
     * @param  $payment
     * @return void
     */
    public function verify($payment)
    {
        $reference = $payment->payment_details['reference'];

        $curl = curl_init();
  
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".env('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
                ),
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
              //Send error Notification to Admin 
              Notification::send($this->getAdmins(), new PaymentVerificationReport($payment,$error));
              return; 
            }
            

            if (json_decode($response)->data->amount !== $payment->payment_details['amount']*100){

                $payment->update([
                     'status' => 'FAILED'
                 ]);

                 
            //Send payment error to Admin
            Notification::send($this->getAdmins(), new PaymentVerificationReport($payment,json_decode($response)));
            return;
            }

            $payment->update([
                'status' => 'CONFIRMED'
            ]);

            Notification::send($this->getAdmins(), new PaymentVerificationReport($payment,['report' => 'Payment confirmed successfully!']));
              
    }

    /**
     * 
     * Fetch all admin users
     * @return User $admins
     */

    public function getAdmins()
    {
        $admins = User::role('admin')->get();

        return $admins;
    }
}
