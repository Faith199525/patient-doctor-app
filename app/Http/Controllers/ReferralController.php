<?php

namespace App\Http\Controllers;

use App\Models\Partners;
use App\Models\Referral;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Events\ReferralEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailForReferral;
use DB;

class ReferralController extends BaseController
{
    public function createReferral(Request $request)
    {
        $user = auth()->user();
        $case= CaseFile::find($request->case_file_id);

        if(! $user->hasRole('doctor') || $user->id != $case->doctor_id){
            return $this->failedResponse('Unauthorized');
        }

        request()->validate([
            'case_file_id' => 'required|exists:case_files,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'doctor_note' => 'string',
            'type' => 'required|in:HOSPITAL,DIAGNOSTIC,PAEDIATRICIAN,GYNAECOLOGIST,OPTICIAN,OPHTHALMOLOGIST,ENT,CARDIOLOGIST,INTERNAL MEDICINE PHYSICIAN,ORTHOPAEDIC SURGEON,PUBLIC HEALTH PHYSICIAN,GENERAL SURGEON,UROLOGIST,GASTROENTEROLOGIST,DERMATOLOGIST,DENTIST,ORAL SURGEON',
        ]);
        $referral = Referral::firstOrCreate(
            ['case_file_id' => $request->get('case_file_id')],
            $request->only('title', 'description', 'doctor_note', 'type')
        );

        /*
        referral is broadcast as real time, and its also sent as an email to concerned people and also saved on notification table in the database. All these notifications are queued to avoid system slow down and its run using a queue worker on localmachine or supervisor on production
        */
        if($request->type == 'HOSPITAL' || $request->type == 'DIAGNOSTIC'){

            $partners= User::role($request->type)->get();
            event(new ReferralEvent($referral, $partners));
            Notification::send($partners, new SendEmailForReferral($referral));

            return $this->successfulResponse(201, $referral);

        } else{

            $special = DB::table('specialties')->where('name', strtolower($request->type))->value('id');
            $specialists =DoctorProfile::where('specialty_id', $special)->get();
            $partners= User::find($specialists->pluck('user_id')->toArray());

            event(new ReferralEvent($referral, $partners));
            Notification::send($partners, new SendEmailForReferral($referral));

            return $this->successfulResponse(201, $referral);

        }
        
    }

    public function acceptReferral(Referral $referral, Request $request)
    {
        $user = auth()->user();

        if($user->hasAnyRole('diagnostic', 'hospital')){

            if ($user->roles->pluck('name')->toArray()[0] != strtolower($referral->type)) {

            return $this->failedResponse('Unauthorized');
        }
        if( $referral->status == 'ACCEPTED' || $referral->status == 'COMPLETED'){
             return response()->json(['message'=>'Sorry, another person has accepted this request' ], 401);
        }
            //'proposed_date' => 'required|date|date_format:Y-m-d|after:yesterday'
        $referral->update([
            'partners_id' => $user->id,
            'status' => Referral::ACCEPTED ]);
       
        $partners= User::find($referral->caseFile->patient_id);//this is patient
        event(new ReferralEvent($referral, $partners));
        Notification::send($partners, new SendEmailForReferral($referral));

        return $this->successfulResponse(202); 

        } elseif ($user->hasRole('doctor')) {

        $specialists =DoctorProfile::where('user_id', $user->id)->value('specialty_id');
        $special = DB::table('specialties')->where('id', $specialists)->value('name');

        if ( strtolower($special) != strtolower($referral->type)) {

            return $this->failedResponse('Unauthorized');
        }
        if( $referral->status == 'ACCEPTED' || $referral->status == 'COMPLETED'){

            if($user->id == $referral->partners_id){

                return response()->json(['message'=>'This request has already been accepted by you' ], 401);
            }

             return response()->json(['message'=>'Sorry, another person has accepted this request' ], 401);
        } 
    
        $referral->update([
            'partners_id' => $user->id,
            'status' => Referral::ACCEPTED ]);
       
        $partners= User::find($referral->caseFile->patient_id);//this is patient
        event(new ReferralEvent($referral, $partners));
        Notification::send($partners, new SendEmailForReferral($referral));

        return $this->successfulResponse(202);

        } else{

            return $this->failedResponse('Unauthorized');
        }
        
    }

   /* public function getALlReferral(Request $request)
    {
        $request->validate([
            'type' => 'required|in:HOSPITAL,DIAGNOSTIC'
        ]);
        $referrals = [];

        $referralQuery = Referral::where('type', $request->get('type'));

        if (request('q') == 'picked') {
            $referrals = $referralQuery->where('status', Referral::ACCEPTED)->get();
            return $this->successfulResponse(200, $referrals);
        }
        if (request('q') == 'all') {
            $referrals = $referralQuery->get();
            return $this->successfulResponse(200, $referrals);
        }

        if (request('q') == 'not-picked') {
            $referrals = $referralQuery->whereNull('partners_id')->get();
            return $this->successfulResponse(200, $referrals);
        }

        if (request('q') == 'patient') {
            $referrals = Referral::query()
                ->join('case_files', 'case_files.id', '=', 'referral.case_file_id')
                ->whereNotNull('referral.partners_id')
                ->where(['case_files.patient_id' => auth()->id(), 'referral.status' => Referral::ACCEPTED])
                ->get();
            return $this->successfulResponse(200, $referrals);
        }

        $diagnostic = auth()->user()->partners()->where('type', Partners::DIAGNOSTIC)->first();

        if ($diagnostic)
            $referrals = $referralQuery->where('partners_id', optional($diagnostic)->id)->get();

        return $this->successfulResponse(200, $referrals);
    }*/

    public function getALlActiveReferral()
    {
        $user = auth()->user();

        if($user->hasAnyRole('diagnostic', 'hospital')){

             $referrals = Referral::where(function ($query) use($user){
                   $query->active();
                   $query->ofType(strtoupper($user->roles->pluck('name')->toArray()[0]) );
                   })->get();

        return response()->json(['data' => $referrals], 200);

        }elseif ($user->hasRole('doctor')) {

             $specialists =DoctorProfile::where('user_id', $user->id)->value('specialty_id');
             $special = DB::table('specialties')->where('id', $specialists)->value('name');
             $referrals = Referral::where(function ($query) use($special){
                   $query->active();
                   $query->ofType(strtoupper($special));
                   })->get();

            return response()->json(['data' => $referrals], 200);

        }else{

            return response()->json(['message'=>'Unauthorized'], 401);
        }
        
    }

    public function getALlAcceptedReferral()
    {
        $user = auth()->user();

        if(! $user->hasAnyRole('diagnostic', 'hospital', 'doctor')){

            return response()->json(['message'=>'Unauthorized'], 401);
        }
            $referrals = Referral::where(function ($query) use($user){
                   $query->accepted();
                   $query->where('partners_id', $user->id);
                   })->get();

        return response()->json(['data' => $referrals], 200);
        
    }

    public function getSingleReferral(Referral $referral)
    {
        return $this->successfulResponse(200, $referral);
    }
}
