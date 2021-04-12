<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\UpdateDrugsRequest;
use App\Models\Drug;
use App\Models\Partners;
use App\Models\CaseFile;
use App\Models\User;
use App\Events\PrescriptionEvent;
use App\Events\PharmaciesEvent;
use App\Models\Prescription;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\PrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailForPrescription;
use App\Notifications\SendEmailForPatientCaseFile;

class PrescriptionController extends BaseController
{
    /**
     * @var PrescriptionService
     */
    private $prescriptionService;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(PrescriptionService $prescriptionService, UserRepository $userRepository)
    {
        $this->prescriptionService = $prescriptionService;
        $this->userRepository = $userRepository;
    }

    public function createPrescription(PrescriptionRequest $request)
    {
        //$prescription = $this->prescriptionService->createPrescription($request->all()); 
        $prescription = $this->prescriptionService->createPrescription($request->all());

        $drug = $this->prescriptionService->addDrugToPrescription($prescription, $request->all());

        $pharmacy = User::role('PHARMACY')->with('partners')->take(10)->get();
         /*
        prescription is broadcast as real time using event broadcast, and its also sent as an email to concerned people and also saved on notification table in the database using Notification send. All these notifications are queued to avoid system slow down and its run using a queue worker on localmachine or supervisor on production
        */ 
        $caseFile = CaseFile::find($request->case_file_id);
        event(new PharmaciesEvent($caseFile, $pharmacy->toJson()));//this toJson helps to pass eager relationship to events brodacast

        return $this->successfulResponse(201, $prescription, 'Successfully created');
    }

    public function getPharmacies()
    {
        $pharmacy = User::role('PHARMACY')->with('partners')->take(10)->get();
        
        return response()->json([
            "data" => $pharmacy,
            'message'=>'Successfully'
        ], 200);
    }

     public function getPatientNewPrescription()
    {
        // $prescriptions = Prescription::query()
        // ->join('case_files', 'case_files.id', '=', 'prescriptions.case_file_id')
        // ->where('case_files.patient_id', auth()->id())
        // ->whereIn('prescriptions.status', ['ACTIVE', 'PENDING'])
        // ->get();

        $prescriptions = Prescription::query()
                    ->whereIn('status', ['ACTIVE', 'PENDING'])
                    ->whereHas('caseFile', function($query){
                    $query->where('patient_id',auth()->id());
                    })->get();

        return $this->successfulResponse(200, $prescriptions);
    }

    public function getPatientPending()
    {
        $prescriptions = Prescription::query()
                    ->where('status', 'ACCEPTED')
                    ->whereHas('caseFile', function($query){
                    $query->where('patient_id',auth()->id());
                    })->get();

        return $this->successfulResponse(200, $prescriptions);
    }

    public function getPatientCompleted()
    {
       $prescriptions = Prescription::query()
                    ->where('status', 'APPROVED')
                    ->whereHas('caseFile', function($query){
                    $query->where('patient_id',auth()->id());
                    })->get();

        return $this->successfulResponse(200, $prescriptions);
    }

    public function getDeclined()
    {
       $prescriptions = Prescription::query()
                    ->where('status', 'DECLINED')
                    ->whereHas('caseFile', function($query){
                    $query->where('patient_id',auth()->id());
                    })->get();

        return $this->successfulResponse(200, $prescriptions);
    }

    public function getUnassignedPrescriptions($casefile)
    {

        $prescription = Prescription::where(function ($query) use ($casefile) {
                                        $query->where('case_file_id', $casefile);
                                        })->where(function ($query) {
                                            $query->where('status', 'ACTIVE')
                                                ->orWhere('status', 'REJECTED');
                                        })->count();
        if($prescription == 0){

            return 'Assigned';       
        }      
        
        $pharmacy = User::role('PHARMACY')->with('partners')->take(10)->get();
        $caseFile = CaseFile::find($casefile);

        $data = array('pharmacy' => $pharmacy, 'caseFile'=> $caseFile);

        return response()->json(["data" => $data, 'message'=>'Successfully'], 200);
    }

    // public function getAllPrescription()
    // {
    //     $prescriptions = [];
    //     if (request('q') == 'picked'){
    //         $prescriptions = Prescription::where('status', Prescription::ACCEPTED)->get();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }
    //     if (request('q') == 'all'){
    //         $prescriptions = Prescription::all();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }

    //     if (request('q') == 'not-picked'){
    //         $prescriptions = Prescription::whereNull('partners_id')->get();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }

    //     if (request('q') == 'patient'){
    //         $prescriptions = Prescription::query()
    //             ->join('case_files', 'case_files.id', '=', 'prescriptions.case_file_id')
    //             ->whereNotNull('prescriptions.partners_id')
    //             ->where(['case_files.patient_id' => auth()->id(), 'prescriptions.status' => Prescription::ACCEPTED])
    //             ->get();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }
    //     if (request('q') == 'active'){
    //         $prescriptions = Prescription::query()
    //             ->join('case_files', 'case_files.id', '=', 'prescriptions.case_file_id')
    //             ->where(['case_files.patient_id' => auth()->id(), 'prescriptions.status' => Prescription::ACTIVE])
    //             ->get();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }

    //     if (request('q') == 'completed'){
    //         $prescriptions = Prescription::query()
    //             ->join('case_files', 'case_files.id', '=', 'prescriptions.case_file_id')
    //             ->whereNotNull('prescriptions.partners_id')
    //             ->where(['case_files.patient_id' => auth()->id(), 'prescriptions.status' => Prescription::APPROVED])
    //             ->get();
    //         return $this->successfulResponse(200, $prescriptions);
    //     }

    //     $pharmacy = auth()->user()->partners()->where('type', Partners::PHARMACY)->first();

    //     if ($pharmacy)
    //     $prescriptions = Prescription::where('partners_id', optional($pharmacy)->id)->get();

    //     return $this->successfulResponse(200, $prescriptions);
    // }

    // public function updatePrescription(PrescriptionRequest $request, Prescription $prescription)
    // {
    //     $prescription = $this->prescriptionService->updatePrescription($prescription, $request->all());

    //     $drug = $this->prescriptionService->addDrugToPrescription($prescription, $request->all());

    //     $case = CaseFile::find($prescription->case_file_id);
    //     $patient = User::find($case->patient_id);
    //     event(new PrescriptionEvent($prescription));
    //     Notification::send($patient, new SendEmailForPrescription($prescription));

    //     return $this->successfulResponse(200, $prescription);
    // }

    public function getPrescription(Prescription $prescription)
    {
        //$prescription= Prescription::with('caseFile')->find($prescription);
        return $this->successfulResponse(200, $prescription);
    }

    public function getNewPrescription()
    {
        $partner = auth()->user();
        $prescription = Prescription::where('partners_id', $partner->id)
                        ->where('status', 'PENDING')
                        ->get();
        return $this->successfulResponse(200, $prescription);
    }

    public function getPending()
    {
        $partner = auth()->user();
        $prescription = Prescription::where('partners_id', $partner->id)
                        ->where('status', 'ACCEPTED')
                        ->get();
        return $this->successfulResponse(200, $prescription);
    }

    public function getCompleted()
    {
        $partner = auth()->user();
        $prescription = Prescription::where('partners_id', $partner->id)
                        ->where('status', 'APPROVED')
                        ->get();
        return $this->successfulResponse(200, $prescription);
    }

    public function attachPharmacyToPrescription(Prescription $prescription, Request $request)//patient chooses a pharmacy
    {
        $request->validate([
            //'partners_id' => 'required|integer|exists:partners,id'
            'partners_id' => 'required',
            'status' => 'required'
        ]);

        // if($prescription->partners_id != Null){
        //     return response()->json(['message'=>'Already Assigned', 200]); 
        // }

       $this->prescriptionService->attachPharmacyToPrescription($prescription, $request->only(['partners_id','status']));

        $partner = User::find($prescription->partners_id);
        $partner->notify(new SendEmailForPrescription($prescription));

        $caseFile = CaseFile::find($prescription->case_file_id);
        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new SendEmailForPrescription($prescription));

        return $this->successfulResponse(202, $prescription);
    }

    public function deleteDrug(Prescription $prescription, Drug $drug)
    {
        if (! $prescription->drugs->contains($drug)){
            return $this->failedResponse('Drug does not belong to prescription.');
        }
        $drug->delete();

        return $this->successfulResponse(202);
    }

    public function updateDrugs(Prescription $prescription, UpdateDrugsRequest $request)
    {
        DB::transaction(function () use ($prescription, $request) {
            $drugs = $request->get('drugs');
            if ($request->has('pharmacy_note')) {
                $this->prescriptionService->updatePrescription($prescription, ['pharmacy_note' => $request->get('pharmacy_note')]);
            }
             collect($drugs)->each(function ($item) use ($prescription) {
                 $item = (object)$item;
                $drug = Drug::findOrFail($item->id);
                if ($drug->prescription_id == $prescription->id){
                    $drug->update(['price_in_minor_unit' => $item->price]);
                }
             });
        },5);

         $request->validate([
            'status' => 'required|in:ACCEPTED'
        ]);
        $prescription->update([
            'status'=> $request->status]);

        $caseFile = CaseFile::find($prescription->case_file_id);
        $patient = User::find($caseFile->patient_id);
        $patient->notify(new SendEmailForPrescription($prescription));

        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new SendEmailForPrescription($prescription));

        return $this->successfulResponse(200);
    }

    public function pharmacyDeclinePrescription(Prescription $prescription, Request $request)
    {
        $prescription->update([
            'partners_id'=> Null,
            'status'=> 'REJECTED']);

        $pharmacy = User::role('PHARMACY')->with('partners')
                    ->where('id', '!=', auth()->id())->take(10)->get();
        $caseFile = CaseFile::find($prescription->case_file_id);
        $patient = User::find($caseFile->patient_id);
        event(new PharmaciesEvent($caseFile, $pharmacy->toJson()));
        $patient->notify(new SendEmailForPrescription($prescription));

        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new SendEmailForPrescription($prescription));

        return $this->successfulResponse(200);

    }

    public function patientAcceptPrescriptionCost(Prescription $prescription, Request $request)
    {
        $request->validate([
            'delivery_type' => 'required|in:PICK-UP',
            'status' => 'required|in:APPROVED'
        ]);
        $user = auth()->user();
        if($prescription->caseFile->patient_id != $user->id){
            return $this->failedResponse();
        }
        $this->prescriptionService->updatePrescription(
            $prescription,
            $request->only('delivery_type', 'status')
        );

        $sub =auth()->user()->subscriptions()->where('active',true)->first();
        $sub->update(['bonus'=> $request->bonus]);

        $pharmacy = User::find($prescription->partners_id);
        $caseFile = CaseFile::find($prescription->case_file_id);
        $pharmacy->notify(new SendEmailForPrescription($prescription));

        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new SendEmailForPrescription($prescription));
        
        return $this->successfulResponse(202);
    }

    public function patientDeclinePrescriptionCost(Prescription $prescription, Request $request)
    {
       $prescription->update([
            'status'=> 'DECLINED']);

        $user = User::find($prescription->partners_id);
        $caseFile = CaseFile::find($prescription->case_file_id);
        $user->notify(new SendEmailForPrescription($prescription));

        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new SendEmailForPrescription($prescription));

        return $this->successfulResponse(200);

    }
}
