<?php
namespace App\Service;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RestClient{

    public $paystack_key;
    public $paystack_url;

    public function __construct(){
        
    
        $this->paystack_key = config('credentials.paystack');
        $this->paystack_url = config('credentials.paystack_url');
        $this->paystack = new \Yabacon\Paystack($this->paystack_key);
       
    
    }

    public function getCheckoutUrl(){
        try{
            
            $data = $this->paystack->transaction->initialize([
                'amount'=>request()->amount * 100,
                'email'=> auth()->user()->email
            ]);

            if(!$data){
                return response(["data"=>$data, 'statusCode' => 500],500);
            }
            if(request()->type =='premium' && request()->plan =='anually'){
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
                'bonus' => '2000',
            ]);
            } 
            else if(request()->type =='premium' && request()->plan =='quaterly'){

                $plan= Subscription::where('user_id',auth()->user()->id)->where('type','premium')->where('plan', 'quaterly')->latest()->first();
                if(!$plan){
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
                'bonus' => '0',
            ]);
                } if($plan->bonus == '0'){
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
                'bonus' => '1000',
            ]); 
                } if($plan->bonus == '1000'){
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
                'bonus' => '0',
            ]); 
                } else{
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
                'bonus' => '0',
            ]); 
                }
            } else{
                auth()->user()->subscriptions()->create([
                'plan' => request()->plan,
                'type' => request()->type,
                'payment_status' => 'pending',
                'amount' => request()->amount * 1000,
                'reference' => $data->data->reference,
            ]);
            }

            
            return response(["data"=>$data->data->authorization_url, "message"=> $data->message, 'statusCode' => 200], 200);

        } catch(\Yabacon\Paystack\Exception\ApiException $e){

            print_r($e->getResponseObject());
            die($e->getMessage());

        }
    }

    public function verifyPayment($request){
        try
        {
            $reference = $request->trxref;
            $tranx = $this->paystack->transaction->verify([
                'reference'=>$reference
            ]);
            if($tranx->data->status !== "success"){
                return response(["data"=>false, "message"=> "Payment unsuccessful.", 'statusCode' => 400], 400);
            }

            $sub = Subscription::where('reference', $reference)->first();
            if(!$sub){
                return response(["data"=>false, "message"=> "Something went wrong while processing this payment.", 'statusCode' => 400], 400);
            }


            $expiry_date = $this->calculateExpiry($sub->plan);
            $sub->update(['active'=>true,'payment_status'=>'paid','start'=>Carbon::now(),'end'=>$expiry_date]);
            $txramnt = $sub->amount;
            $sub->transaction()->create(['amount' => $txramnt]);


            return response(["data"=>true, "message"=> "Payment made successfully.", 'statusCode' => 200], 200);

        } catch(\Yabacon\Paystack\Exception\ApiException $e){

          print_r($e->getResponseObject());
          die($e->getMessage());

        }
    }

    public function calculateExpiry($plan){
        switch ($plan) {
            case 'monthly':
                return Carbon::now()->addMonths(1);
                break;
            case 'quaterly':
                return Carbon::now()->addMonths(6);
                break;
            case 'anually':
                return Carbon::now()->addMonths(12);
                break;
            default:
                return null;
                break;
        }
    }

    
}
