<?php

namespace App\Http\Controllers;

use App\Models\MedicalHistory;
use Illuminate\Http\Request;
use Validator;

class MedicalHistoryController extends BaseController
{

    /**
     * Store or Update a Patient's Medical History resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medical_info' => 'required',
            'additional_info' => 'string|nullable',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $data = [
             'medical_info' => json_decode($request->medical_info,true),
             'additional_info' => $request->additional_info
        ];

         if(auth()->user()->medicalHistory){
            // 'User has medical history so we update'
            auth()->user()->medicalHistory()->update($data);
            $medicalHistory = MedicalHistory::where('patient_id',auth()->id())->first();
         } else {
            // 'User has no medical history so we create'
             $medicalHistory = auth()->user()->medicalHistory()->create($data);
             
         }

        return $this->successfulResponse(200, $medicalHistory,'Medical History Saved!');
    }

    /**
     * Display the Medical History by the specified id.
     *
     * @param  \App\Models\MedicalHistory  $medicalHistory
     * @return \Illuminate\Http\Response
     */
    public function show(MedicalHistory $medicalHistory)
    {
        return $this->successfulResponse(200, $medicalHistory);
    }

     /**
     * Display the logged in Patient's Medical History.
     *
     * @return \Illuminate\Http\Response
     */

    public function getMyMedicalHistory()
    {
        $medicalHistory = auth()->user()->medicalHistory;

        return $this->successfulResponse(200, $medicalHistory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedicalHistory  $medicalHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalHistory $medicalHistory)
    {
        //
    }
}
