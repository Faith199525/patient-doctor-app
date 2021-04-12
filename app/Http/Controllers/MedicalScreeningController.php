<?php

namespace App\Http\Controllers;

use App\Models\MedicalScreening;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class MedicalScreeningController extends BaseController
{
    /**
     * Display a listing of screening Centers.
     *
     * @return \Illuminate\Http\Response
     */
    public function screeningCenters()
    {
        $screeningCenters = User::role('diagnostic')->get();

        return $this->successfulResponse(200, $screeningCenters);
    }

    /**
     * Store a new medical screening test.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tests' => 'required',
            'diagnosticId' => 'required|exists:users,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $medicalScreening = $request->user()->medicalScreeningTests()->create([
            'tests' => $request->tests,
            'diagnostic_id' => $request->diagnosticId
        ]);

        return $this->successfulResponse(200, $medicalScreening,'Tests saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MedicalScreening  $medicalScreening
     * @return \Illuminate\Http\Response
     */
    public function show(MedicalScreening $medicalScreening)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicalScreening  $medicalScreening
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicalScreening $medicalScreening)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedicalScreening  $medicalScreening
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalScreening $medicalScreening)
    {
        //
    }
}
