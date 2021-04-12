<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\GeneralPractitionerService;
use Illuminate\Http\Request;
use Validator;

class GeneralPractitionerServiceController extends BaseController
{
    /**
     * Display a listing of General Practitioners.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $generalPractitioners = User::role('doctor')->get()->filter(function($doctor){
            if($doctor->doctorProfile){
                return $doctor->doctorProfile->specialty->code == 'SPELTY001';
            }
        });

        return $this->successfulResponse(200, $generalPractitioners);
    }

    /**
     * Store a new General Practititoner Service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'complaint' => 'required|string',
            'no_of_days' => 'required|numeric|min:1',
            'start_date' => 'required|string',
            'comment' => 'nullable',
            'gp_id' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $gpService = $request->user()->gpServiceRequest()->create($validator->validated());

        return $this->successfulResponse(200, $gpService,'GP Request Service saved successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GeneralPractitionerService  $generalPractitionerService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GeneralPractitionerService $generalPractitionerService)
    {
        //
    }

}
