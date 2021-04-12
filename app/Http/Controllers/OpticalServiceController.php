<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class OpticalServiceController extends BaseController
{
    /**
     * Display a listing of optical Centers.
     *
     * @return \Illuminate\Http\Response
     */
    public function opticalCenters()
    {
        $opticalCenters = User::role('doctor')->get()->filter(function($doctor){
            if($doctor->doctorProfile){
                return $doctor->doctorProfile->specialty->code == 'SPELTY004';
            }
        });

        return $this->successfulResponse(200, $opticalCenters);
    }

     /**
     * Store a new Optical Service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serviceDetails' => 'nullable',
            'opticianId' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $opticalService = $request->user()->opticalServiceRequest()->create([
            'service_details' => $request->serviceDetails,
            'optician_id' => $request->opticianId
        ]);

        return $this->successfulResponse(200, $opticalService,'Optical service request saved successfully!');
    }
}
