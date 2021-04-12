<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Models\Test;
use App\Models\DiagnosticTest;
use App\Events\DiagnosticEvent;
use App\Models\User;
use App\Models\CaseFile;
use App\Models\TestResult;
use App\Notifications\NotifyDiagnosis;
use Illuminate\Support\Facades\Storage;

class DiagnosisController extends Controller
{
    public function sendTest(Request $request)
    {
    	$diagnosis= Diagnosis::updateOrCreate(
    						['case_file_id'=> $request->case_file_id],
    						['status' => 'PENDING']);

        foreach ($request->get('patientTests') as $test ) {
            $diagnosis->tests()->create([
                                        'name'=>$test['name'],
                                        'price_in_minor_unit'=>$test['price_in_naira']]);
        }

        $diagnostic = User::role('DIAGNOSTIC')->with('partners')->take(10)->get();
      
        $caseFile = CaseFile::find($request->case_file_id);
        event(new DiagnosticEvent($caseFile, $diagnostic->toJson()));//this toJson helps to pass eager

        return response()->json(["data" => $diagnosis,'message'=>'Test successfully sent'], 200); 
    }

    public function getDiagnosticTests()
    {
       $tests = DiagnosticTest::all();
       return response()->json(["data" => $tests], 200); 

    }

    public function listDiagnostics()
    {
        $diagnostic = User::role('DIAGNOSTIC')->with('partners')->take(10)->get();

        return response()->json(["data" => $diagnostic], 200); 
    }

    public function referDiagnostic(Diagnosis $diagnosis, Request $request)
    {
        $diagnosis->update([
                            'partners_id'=> $request->partners_id,
                            'status'=> 'ACTIVE']);

        $partner = User::find($diagnosis->partners_id);
        $partner->notify(new NotifyDiagnosis($diagnosis));

        $caseFile = CaseFile::find($diagnosis->case_file_id);
        $doctor = User::find($caseFile->doctor_id);
        $doctor->notify(new NotifyDiagnosis($diagnosis));

        return response()->json(["data" => $diagnosis,'message'=>'Successful'], 200); 
    }

    public function getUnassignedDiagosis($case_file_id)
    {
        $diagnosis= Diagnosis::where('case_file_id', $case_file_id)
                                ->where('status','PENDING')
                                ->count();
        if($diagnosis == 0){

            return 'Assigned';       
        }
        
        $diagnostic = User::role('DIAGNOSTIC')->with('partners')->take(10)->get();
        $caseFile = CaseFile::find($case_file_id);

        $data = array('diagnostic' => $diagnostic, 'caseFile'=> $caseFile);

        return response()->json(["data" => $data], 200);
    }

    public function getTestsAssignedToMe()
    {
        $partner = auth()->user();
        $diagnosis = Diagnosis::where('partners_id', $partner->id)
                        ->where('status', 'ACTIVE')
                        ->get();

        return response()->json(["data" => $diagnosis], 200);
    }

    public function getPatientNewTest()
    {
        //  $diagnosis = Diagnosis::query()
        //             ->whereIn('status', ['ACTIVE', 'PENDING'])
        //             ->whereHas('caseFile', function($query){
        //             $query->where('patient_id',auth()->id());
        //             })->get();

        $diagnosis = Diagnosis::query()
                ->where('status', 'ACTIVE')
                ->whereHas('caseFile', function($query){
                $query->where('patient_id',auth()->id());
                })->get();

        return response()->json(["data" => $diagnosis], 200);
    }

    public function getCompletedTestsByDiagnostic()
    {
        $partner = auth()->user();
        $diagnosis = Diagnosis::where('partners_id', $partner->id)
                        ->where('status', 'COMPLETED')
                        ->get();

        return response()->json(["data" => $diagnosis], 200);
    }

    public function getPatientCompletedTest()
    {
        $diagnosis = Diagnosis::query()
                    ->where('status', 'COMPLETED')
                    ->whereHas('caseFile', function($query){
                    $query->where('patient_id',auth()->id());
                    })->get();

        return response()->json(["data" => $diagnosis], 200);
    }

    public function getDiagnosis(Diagnosis $diagnosis)
    {
        return response()->json(["data" => $diagnosis], 200);
    }

    public function complete(Diagnosis $diagnosis, Request $request)
    {
        $test= Test::where('diagnosis_id', $diagnosis->id)
                                ->whereNull('result')
                                ->count();
        if($test == 0){

            $diagnosis->update(['status'=>'COMPLETED']);

            $caseFile = CaseFile::find($diagnosis->case_file_id);
            $patient = User::find($caseFile->patient_id);
            $patient->notify(new NotifyDiagnosis($diagnosis));

            $doctor = User::find($caseFile->doctor_id);
            $doctor->notify(new NotifyDiagnosis($diagnosis));

            return 'Test Result Complete';       
        }

        return 'Test Result Incomplete';
    }

    public function uploadTestResult(Test $test, Request $request)
    {
        //faithlab@test.com
          $rules = [
            'file' => 'required|mimes:doc,docx,pdf',
        ]; 
        $validator= \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
        return response()->json(
            $validator->errors(),400);
    }
        $file = $request->file;
        //$extention = 'pdf';     

         $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . strtotime(now()) . "." . $file->clientExtension();
            $fileContents = file_get_contents($file->getRealPath());
            Storage::disk('test_results')->put($fileName, $fileContents);
            $test->update([
                'result' => $fileName
            ]);

        return response()->json(["message" => 'Test Results Successfully uploaded'], 200);
    }

    public function getDownload(Test $test)
    {

        $filename = $test->result;

           if(env('APP_ENV') == 'production'){

            return Storage::disk('test_results')->download($filename); 
        }

        return response()->download(storage_path("app/public/test_results/{$filename}"));

    }

    public function viewResult(Test $test)
    {

        $filename = $test->result;  
        
          if(env('APP_ENV') == 'production'){

           // return Storage::disk('test_results')->get($filename);
           return Storage::disk('test_results')->response($filename);
    
        } 
           

        return response()->file(storage_path("app/public/test_results/{$filename}"));

    }

}
