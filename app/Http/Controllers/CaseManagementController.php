<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Events\PatientCaseFile; 
use App\Events\CaseAccepted;
use App\Events\CaseCreated;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailForPatientCaseFile;
use App\Notifications\DoctorRequest;
use App\Notifications\CaseClosed;

class CaseManagementController extends BaseController
{
    public function createCase(Request $request)
    {
        $request->validate([
            'initial_complain' => 'required|string',
            'specialty_id' => 'numeric'
        ]);

        $caseFile = CaseFile::create([
            'patient_id' => auth()->id(),
            'initial_complain' => $request->get('initial_complain'),
            'specialty_id' =>  $request->specialty_id ?: 1
        ]);

        broadcast(new CaseCreated($caseFile))->toOthers();
        
        $id = $caseFile->specialty_id;
        //Notify the Doctor by specialty that Patient requests for
        $doctors = User::all()->filter(function ($user, $key) use ($id) {
            if($user->doctorProfile){
                return ($user->hasRole('doctor') && ((int)$user->doctorProfile->specialty_id === (int)$id));
            }
        });
            
            // $doctors = User::role('doctor')->get()->filter(function($doctor) use ($id){
            //     return (int)$doctor->doctorProfile->specialty_id === (int)$id;
            // });
            
        Notification::send($doctors, new DoctorRequest($caseFile));

        return $this->successfulResponse(201, $caseFile, 'Case file created successfully');
    }

    public function acceptCase(CaseFile $caseFile)
    {
        $caseFile->update(['doctor_id' => auth()->id(), 'status' => 'ACTIVE']);

        broadcast(new CaseAccepted($caseFile))->toOthers();

        return $this->successfulResponse('200', $caseFile, 'Case accepted successfully.');
    }

    public function showUnattendedGeneralCases()
    {
        $id = auth()->user()->doctorProfile->specialty_id;

        //Return only cases to be attended to by a General Practitioner

        if($id == 1){
          $caseFiles = CaseFile::with('patient')->where('doctor_id', null)->where('specialty_id',1)->get();
        } else {
          $caseFiles = [];
        }
        
        return $this->successfulResponse(200, $caseFiles);
    }

    public function showUnattendedSpecialistCases()
    {
        $id = auth()->user()->doctorProfile->specialty_id;

        if($id == 1){
          return $this->successfulResponse(200, []);
        }
        
        $caseFiles = CaseFile::with('patient')->where('doctor_id', null)->where('specialty_id','!=',1)->get();

        //Return only cases to be attended to by a specialist
        $caseFiles = $caseFiles->filter(function ($casefile, $key) use ($id) {
            return ((int)$casefile->specialty_id == (int)$id);
        });

        return $this->successfulResponse(200, $caseFiles);
    }

    public function showSingleCase(CaseFile $caseFile) 
    {
        return $this->successfulResponse(200, $caseFile);
    }

     public function getPatient($id)
     {
        $casefile = CaseFile::whereId($id)->first();
        $user = $casefile->patient;
        $user['initial_complain'] = $casefile->initial_complain;
        $user['doctor_observation'] = $casefile->doctor_observation;
        return $this->successfulResponse(200, $user);
     }


     public function getDoctor($id)
     {
        $casefile = CaseFile::whereId($id)->first();
        $user = User::whereId($casefile->doctor_id)->first();
        $user['initial_complain'] = $casefile->initial_complain;
        return $this->successfulResponse(200, $user);
     }

     public function getPrescription($id){

         $prescription = Prescription::where('case_file_id', $id)->first();
        return $this->successfulResponse(200, $prescription);
     }

    /**
     * Doctor closes a case file
     *
     * @param CaseFile $caseFile
     * @return JsonResponse
     */

    public function closeCase(CaseFile $caseFile)
    {
        $caseFile->update(['status' => 'COMPLETED']);

        $patient= User::find($caseFile->patient_id);
        $patient->notify(new CaseClosed($caseFile));

        return $this->successfulResponse(200, $caseFile, 'Case closed successfully.');
    }
    public function updateCaseFile(CaseFile $caseFile, Request $request)
    {
        $request->validate([
            'doctor_observation' => 'required|string'
        ]);
        $caseFile->update(['doctor_observation' => $request->get('doctor_observation')]);

        return $caseFile;
    }

    public function getActiveCases()
    {
        $caseFiles = auth()->user()->caseFiles()->where('status','ACTIVE')->get();

        return $this->successfulResponse(200, $caseFiles);
    }

    public function getCompletedCases()
    {
        $caseFiles = auth()->user()->caseFiles()->where('status','COMPLETED')->get();

        return $this->successfulResponse(200, $caseFiles);
    }

    public function getActiveCasesForDoctor()
    {
        $caseFiles = CaseFile::with('patient')
                                ->where('doctor_id', auth()->id())
                                ->where('status','ACTIVE')
                                ->get();
        return $this->successfulResponse(200, $caseFiles);
    }

    public function getCompletedCasesForDoctor()
    {

        $caseFiles = CaseFile::with('patient')
                                ->where('doctor_id', auth()->id())
                                ->where('status','COMPLETED')
                                ->get();
        return $this->successfulResponse(200, $caseFiles);
    }

    public function getAllCasesForDoctor()
    {
        $caseFiles = CaseFile::with('patient')
                                ->where('doctor_id', auth()->id())
                                ->orderBy('created_at','desc')
                                ->get();
        return $this->successfulResponse(200, $caseFiles);
    }
    

    public function updateCase(Request $request,CaseFile $caseFile)
    {
        $validator = Validator::make($request->all(), [
            'doctor_name' => 'string',
            'hospital_date' => 'date',
            'hospital_diagnosis' => 'string',
            'hospital_medications' => 'string',
            'hospital_comments' => 'string',
            'status' => 'in:ACTIVE,COMPLETED,PENDING'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }
        
        $caseFile->update($validator->validated());

        
        return $this->successfulResponse(200, $caseFile,'Case File Updated!');

    }

    public function getPatientRecentCases(User $user)
    {
        $recentCases = $user->caseFiles()->orderBy('created_at', 'desc')->paginate(5);
        return $this->successfulResponse(200, $recentCases);
    }

}
