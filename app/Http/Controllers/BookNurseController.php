<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookNurse;
use App\Events\NurseAndNutritionist;
use App\Models\User;
use App\Models\Partners;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyNurseRequest;

class BookNurseController extends Controller
{
    public function book(Request $request)
    {
    	$rules =[
            'date'=> 'required|date|date_format:Y-m-d',
            'days' => 'required',
            'description' => 'required',
            'address' => 'required',
            'comment' => 'nullable',
            'partner_id' => 'nullable',
    	];

        $validator= \Validator::make($request->all(), $rules);
    	if ($validator->fails()) {
        return response()->json(
        	$validator->errors(),400);
    } 
        $user= auth()->user();

        $booking = new BookNurse;
        $booking->date=Carbon::parse($request->date);
        $booking->days= $request->days;
        $booking->description= $request->description;
        $booking->comment= $request->comment;
        $booking->status= 'ACTIVE';
        $booking->address= $request->address;
        $booking->partner_id= $request->partner_id;

        $booking->patient()->associate($user);
        $booking->save();

        $partner = User::find($request->partner_id);
        $partner->notify(new NotifyNurseRequest($booking));

    	return response()->json(["data" => $booking,'message'=>'Booking successful'], 201);
    }

    public function listNurses()
    {
        // $nurses = User::role('NURSE')->with('requestedPartner')->take(10)->get();
        $nurses = User::role('NURSE')->with('requestedPartner')
                  ->whereDoesntHave('nurse', function($query){
                    $query->where('status','CONFIRMED');
                  })
                  ->take(10)->get();

        //  $doctor = User::role('DOCTOR')->with('doctorProfile')->whereHas('doctorProfile', function ($query) {
        // $query->where('specialty_id', '=', '1');
        // })->get();
        return response()->json(["data" => $nurses], 200);
    }

    // public function referNurse(BookNurse $booking, Request $request)
    // {
    //     $booking->update([
    //                         'partner_id'=> $request->partner_id,
    //                         'status'=> 'ACTIVE']);

    //     $partner = User::find($request->partner_id);
    //     $partner->notify(new NotifyNurseRequest($booking));

    //     return response()->json(["data" => $booking,'message'=>'Successful'], 200); 
    // }

   // public function updatebooking(Nurse $booking, Request $request)
   //  {
   //      $rules =[
   //          'date'=> 'required|date|date_format:Y-m-d',
   //          'days' => 'nullable',
   //          'hours' => 'nullable',
   //          'type' => 'required|in:NURSE,NUTRITIONIST',
   //          'description' => 'required',
   //          'address' => 'required',
   //          'comment' => 'nullable',
   //      ];

   //      $validator= \Validator::make($request->all(), $rules);
   //      if ($validator->fails()) {
   //      return response()->json(
   //          $validator->errors(),400);
   //  } 
   //      $user = auth()->user();
   //      if(! $user->hasRole('patient') ||  $user->id != $booking->patient_id){
   //          return response()->json([
   //          'message'=>'Unauthorized'
   //      ], 401);
   //  }
   //      $booking->update([
   //          'date' => $request->date, 
   //          'days' => $request->days,
   //          'hours' => $request->hours,
   //          'description' => $request->description,
   //          'type' => $request->type,
   //          'address' => $request->address,
   //          'comment' => $request->comment]);

   //        return response()->json(["data" => $booking,'message'=>'Booking successfully updated'], 200);
   //  }

    public function confirm(BookNurse $booking, Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'status' => 'required|in:CONFIRMED'
        ]);
        $booking->update([
        	'status' => $request->status]);

        $patient= User::find($booking->patient_id);
        $patient->notify(new NotifyNurseRequest($booking));

        return response()->json([
            'message'=>'Successfully Comfirmed'], 200);
    }

    public function completeABooking(BookNurse $booking, Request $request)
    {
        $user = auth()->user();   

        $request->validate([
            'status' => 'required|in:COMPLETED'
        ]);
        $booking->update($request->only('status'));

        // $patient= User::find($booking->patient_id);
        // $patient->notify(new NotifyNurseRequest($booking));

        return response()->json(['message'=>'Success'], 200);
    }

    public function show (BookNurse $booking)
    {
        $booking = BookNurse::with('appointment')->find($booking);
        return response()->json(['data' => $booking], 200);
    }

    public function showAllConfirmed()
    {
        $user = auth()->user();
    
        $book = BookNurse::where('partner_id', $user->id)->where('status', 'CONFIRMED')->get();

        return response()->json(['data' => $book], 200);
    }

    public function showAllCompleted()
    {
        $user = auth()->user();

        $book = BookNurse::where('partner_id', $user->id)->where('status', 'COMPLETED')->get();

        return response()->json(['data' => $book], 200);
    }

    public function showAllNew()
    {
        $user = auth()->user();

        $book = BookNurse::with('appointment')->where('partner_id', $user->id)->where('status', 'ACTIVE')->get();

        return response()->json(['data' => $book], 200);
    }
}
