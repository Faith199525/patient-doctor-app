<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Callup;
use App\Models\User;
use App\Models\Partners;
use App\Events\AmbulanceCallup;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailForAmbulanceCallup;
use DB;

class AmbulanceCallUpController extends Controller
{
    public function Callup(Request $request)
    {
    	$rules =[
            'pick_up_address' => 'required',
            'phone_number' => 'required|numeric',
    	];

        $validator= \Validator::make($request->all(), $rules);
    	if ($validator->fails()) {
        return response()->json(
        	$validator->errors(),400);
    } 
        $user = auth()->user();

        $callup = new Callup;
        $callup->pick_up_address= $request->pick_up_address;
        $callup->phone_number= $request->phone_number;
        $callup->status= 'PENDING';
        $callup->user()->associate($user);
        $callup->save();
    	/*
        request for ambulance service is broadcast as real time, and its also sent as an email to concerned people and also saved on notification table in the database. All these notifications are queued to avoid system slow down and its run using a queue worker on localmachine or supervisor on production
        */
        $partners= User::role('AMBULANCE')->get();
        Notification::send($partners, new SendEmailForAmbulanceCallup($callup));
        
    	return response()->json(["data" => $callup,'message'=>'CallUp successfully created'], 200);
    }

    public function accept(Callup $callup, Request $request)
    {
        $user = auth()->user();

        if( $callup->status == 'ACCEPTED' || $callup->status == 'COMPLETED'){

            if($user->id == $callup->ambulance_id){

                return response()->json(['message'=>'Already Assigned To You' ], 401);
            }

             return response()->json(['message'=>'Sorry, Already Assigned' ], 401);
        } 

        // $request->validate([
        //     'status' => 'required|in:ACCEPTED'
        // ]);
        $callup->update([
            'status'=> 'ACCEPTED',
            'ambulance_id'=> $user->id]);

        $owner= User::find($callup->user_id);//the person that requested for the service
        $owner->notify(new SendEmailForAmbulanceCallup($callup));

        return response()->json(['message'=>'Success'], 200);
    }

    public function completeACallupRequest(Callup $callup, Request $request)
    {
        $user = auth()->user();

        if($callup->ambulance_id != $user->id){
            return response()->json([
            'message'=>'Unauthorized'
        ], 401);
        }

        $request->validate([
            'status' => 'required|in:COMPLETED'
        ]);
        $callup->update($request->only('status'));

        return response()->json(['message'=>'Success'], 200);
    }

    public function show ($callup)
    {
       $callup = Callup::find($callup);
       return response()->json(['data' => $callup], 200);
    }

    public function showAllPendingCallups()
    {
        $callup = Callup::where('status', 'PENDING')
                ->get();

        return response()->json(['data' => $callup], 200);
    }

    public function showAllAcceptedCallups()
    {
        $user = auth()->user();

        $callup = Callup::where('ambulance_id', $user->id)
                ->where('status', 'ACCEPTED')
                ->get();

        return response()->json(['data' => $callup], 200);
    }

    public function showAllCompletedCallups()
    {
        $user = auth()->user();

        $callup = Callup::where('ambulance_id', $user->id)
                ->where('status', 'COMPLETED')
                ->get();

        return response()->json(['data' => $callup], 200);
    }

    public function getPatientCallups()
    {
        $emergency = Callup::where('user_id', \Auth::user()->id)->get();
        return response()->json(['data' => $emergency], 200);
    }
}
