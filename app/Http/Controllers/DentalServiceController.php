<?php

namespace App\Http\Controllers;

use App\Models\DentalService;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class DentalServiceController extends BaseController
{
    /**
     * Display a listing of Dentists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dentists = User::role('doctor')->get()->filter(function($doctor){
            if($doctor->doctorProfile){
                return $doctor->doctorProfile->specialty->code == 'SPELTY015';
            }
        });

        return $this->successfulResponse(200, $dentists);
    }

    /**
     * Store a newly created Dental Service Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceDetails' => 'nullable',
            'dentistId' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $dentalService = $request->user()->dentalServiceRequest()->create([
            'service_details' => $request->serviceDetails,
            'dentist_id' => $request->dentistId
        ]);

        return $this->successfulResponse(200, $dentalService,'Dental service request saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DentalService  $dentalService
     * @return \Illuminate\Http\Response
     */
    public function show(DentalService $dentalService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DentalService  $dentalService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DentalService $dentalService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DentalService  $dentalService
     * @return \Illuminate\Http\Response
     */
    public function destroy(DentalService $dentalService)
    {
        //
    }
}
