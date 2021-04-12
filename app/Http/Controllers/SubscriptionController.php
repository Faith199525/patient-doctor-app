<?php

namespace App\Http\Controllers;
//use App\Service\RestClient;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use App\Notifications\SubscriptionNotification;
use DB;

class SubscriptionController extends Controller
{
    // public $client;
    // public function __construct(RestClient $client){
    //     $this->client = $client;
    // }

    // public function getCheckoutUrl(SubscriptionRequest $request){
        
    //     return $this->client->getCheckoutUrl();

    // }

    // public function processPayment(Request $request){
        
    //     return $this->client->verifyPayment($request);
    // }

    public function getActiveSubscription(){
        
       $sub = auth()->user()->subscriptions()->where('active',true)->first();
        
        return response(['data'=>$sub, 'statusCode'=>200],200);
    }

    public function getSubscriptionPlans(){
        
        $plans = SubscriptionPlan::all();
        return response()->json(["data" => $plans], 200); 
         
         return response(['data'=>$sub, 'statusCode'=>200],200);
     }

    public function subscribe(Request $request)
    {
        $user = auth()->user();              

         $expiry_date = $this->calculateExpiry($request->plan);
         $start = Carbon::now();

        if($request->type =='Premium' && $request->plan =='annual'){
           $sub= auth()->user()->subscriptions()->create([
            'plan' => $request->plan,
            'type' => $request->type,
            'amount' => $request->amount,
            'start' => $start,
            'end' => $expiry_date,
            'active' => true,
            'bonus' => '4000',
        ]);
        $user->notify(new SubscriptionNotification($sub));
        return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
        }
        if($request->type =='Premium' && $request->plan =='semi_annual'){
            $sub= auth()->user()->subscriptions()->create([
             'plan' => $request->plan,
             'type' => $request->type,
             'amount' => $request->amount,
             'start' => $start,
             'end' => $expiry_date,
             'active' => true,
             'bonus' => '2000',
         ]);
         $user->notify(new SubscriptionNotification($sub));
         return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
        }
        else if($request->type =='Premium' && $request->plan =='quarterly'){

            $plan= Subscription::where('user_id',auth()->user()->id)->latest()->first();         
            if(!$plan){          
                $sub= auth()->user()->subscriptions()->create([
                    'plan' => $request->plan,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'start' => $start,
                    'end' => $expiry_date,
                    'active' => true,
                    'bonus' => '0',
                ]);
                $user->notify(new SubscriptionNotification($sub));
                return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
            }
            else if( !($plan->type == 'Premium' && $plan->plan == 'quarterly')) {
                $sub= auth()->user()->subscriptions()->create([
                    'plan' => $request->plan,
                    'type' => $request->type,   
                    'amount' => $request->amount,
                    'start' => $start,
                    'end' => $expiry_date,
                    'active' => true,
                    'bonus' => '0',
                ]);
                $user->notify(new SubscriptionNotification($sub));
                return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
            } 
            else if($plan->type == 'Premium' && $plan->plan == 'quarterly'){
                  if($plan->bonus == '0'){
                    $sub= auth()->user()->subscriptions()->create([
                    'plan' => $request->plan,
                    'type' => $request->type,    
                    'amount' => $request->amount,
                    'start' => $start,
                    'end' => $expiry_date,
                    'active' => true,
                    'bonus' => '2000',
                  ]); 
                  $user->notify(new SubscriptionNotification($sub));
                  return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
                  }
                  else if($plan->bonus == '2000'){
                    $sub= auth()->user()->subscriptions()->create([
                    'plan' => $request->plan,
                    'type' => $request->type,    
                    'amount' => $request->amount,
                    'start' => $start,
                    'end' => $expiry_date,
                    'active' => true,
                    'bonus' => '0',
                  ]); 
                  $user->notify(new SubscriptionNotification($sub));
                  return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
                 } 
                 else{
                    $sub= auth()->user()->subscriptions()->create([
                    'plan' => $request->plan,
                    'type' => $request->type,    
                    'amount' => $request->amount,
                    'start' => $start,
                    'end' => $expiry_date,
                    'active' => true,
                    'bonus' => '0',
                 ]); 
                 $user->notify(new SubscriptionNotification($sub));
                 return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
                }
        } 
           
        } else  {
            $sub= auth()->user()->subscriptions()->create([
            'plan' => $request->plan,
            'type' => $request->type,
            'amount' => $request->amount,
            'start' => $start,
            'end' => $expiry_date,
            'active' => true
        ]);
        $user->notify(new SubscriptionNotification($sub));
        return response()->json(["data" => $sub,'message'=>'Subscription successful'], 200);
        }

    }

    public function calculateExpiry($plan){
        switch ($plan) {
            case 'Monthly':
                return Carbon::now()->addMonths(1);
                break;
            case 'quarterly':
                return Carbon::now()->addMonths(3);
                break;
            case 'semi_annual':
                    return Carbon::now()->addMonths(6);
                    break;
            case 'annual':
                return Carbon::now()->addMonths(12);
                break;
            default:
                return null;
                break;
        }
    }

}
