<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDoctorRequest;
use App\ServiceContracts\DoctorManagementService;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\User;
use App\Models\Specialty;
use App\RepositoryContracts\DoctorRepository;

class DoctorManagementController extends BaseController
{
    /**
     * @var DoctorManagementService
     */
    private $doctorManagementService;

    public function __construct(DoctorManagementService $doctorManagementService, DoctorRepository $doctorRepository)
    {
        $this->doctorManagementService = $doctorManagementService;
        $this->doctorRepository = $doctorRepository;
    }
    public function createDoctor(CreateDoctorRequest $request)
    {
        $attributes = $request->all();

        $result = $this
            ->doctorManagementService
            ->createDoctor($attributes);


        return $this->successfulResponse(201, $result);
    }

    public function showDoctorProfile()
    {
       $doctorProfile = $this->transformDoctorProfile();
       return $this->successfulResponse(200, $doctorProfile);
    }
    public function updateDoctorProfile(UpdateDoctorRequest $request)
    {

        $user = auth()->user();

        $doctorProfileRequest = ['mcrn','year_of_graduation','specialty_code','school_attended','medical_certificate','working_days','start_time','closing_time','account_name','account_number','bank_name'];

        // if($request->has('specialty_code')){
        //    $request->merge(['specialty_id' => Specialty::where('code',$request->specialty_code)->first()->id]);
        // }


        $doctorProfile = $this->doctorManagementService->saveProfile($request->all());

        if ( $request->medical_certificate != 'undefined') {
            $this->doctorRepository->storeDoctorCertificate($doctorProfile, $request->medical_certificate);
        }
            
        // if (isset( $request->medical_certificate)) {
        //         $this->doctorRepository->storeDoctorCertificate($doctorProfile, $request->medical_certificate);
        //     }

        //$user->doctorProfile->update($request->only(array_merge($doctorProfileRequest,['specialty_id'])));
        $user->update($request->except($doctorProfileRequest));

        $doctor = $this->transformDoctorProfile();

        return $this->successfulResponse(200, $doctor, 'Doctor Profile Updated!');

    }

    /**
     * Transform the doctor profile to avoid nested output
     */
    private function transformDoctorProfile()
    {
        $doctor = User::with('doctorProfile')->find(auth()->id());

        if($doctor->doctorProfile){

            $doctor_profile = $doctor->doctorProfile;
            $specialty = $doctor_profile->specialty;
        
            unset($specialty->id,$doctor_profile->specialty,$doctor_profile->id,$doctor->doctorProfile);
            
            $doctorProfile = array_merge($doctor->toArray(), $doctor_profile->toArray(), $specialty->toArray());

        } else {

            $doctorProfile = array_merge($doctor->toArray(),
            [
                'bank_name' => null,
                'account_name' => null,
                'account_number' => null,
                'mcrn' => null,
                'school_attended' => null,
                'start_time' => null,
                'closing_time' => null,
                'specialty_code' => null,
                'specialty_name' => null
            ]);
        }

        return $doctorProfile;
    }
}
