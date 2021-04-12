<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\OpticalService;
use App\Models\MedicalScreening;
use App\Models\GeneralPractitionerService;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MedicalScreeningAppointment; 
use App\Notifications\OpticalServiceRequest; 
use App\Notifications\DentalServiceRequest;
use App\Notifications\BookedAppointment;
use App\Notifications\GPServiceRequest;
use App\Models\BookNurse;
use App\Models\DentalService;

class AppointmentController extends BaseController
{

    /**
     * Display a list of appointments for patients
     * @return \Illuminate\Http\Response
     */

    public function getPatientAppointments()
    {
        $user = auth()->user();

        $medicalTests = $user->medicalScreeningTests;
        $opticalServiceRequests = $user->opticalServiceRequest;
        $dentalServiceRequests = $user->dentalServiceRequest;
        $gpServiceRequests = $user->gpServiceRequest;
        $bookNurseRequests = $user->bookNurse;


        $appointments = $medicalTests->map(function ($test, $key) {
            return $test->appointment;
        });

        $appointments = $appointments->merge(
            $opticalServiceRequests->map(function ($request, $key) {
            return $request->appointment;
         })
        );

        $appointments = $appointments->merge(
            $gpServiceRequests->map(function ($request, $key) {
            return $request->appointment;
         })
        );

        $appointments = $appointments->merge(
            $bookNurseRequests->map(function ($request, $key) {
            return $request->appointment;
         })
        );

        $appointments = $appointments->merge(
            $dentalServiceRequests->map(function ($request, $key) {
            return $request->appointment;
         })
        );
        
        return $this->successfulResponse(200, $appointments);
    }

    /**
     * Display a list of appointments for centers
     * @return \Illuminate\Http\Response
     */

    public function getCenterAppointments()
    {
        $user = auth()->user();

        $medicalTests = $user->medicalScreening;
        $opticalServices = $user->opticalService;
        $gpServices = $user->gpService;
        $dentalServices = $user->dentalService;

        $appointments = $medicalTests->map(function ($test, $key) {
            return $test->appointment;
        });

        $appointments = $appointments->merge(
            $opticalServices->map(function ($service, $key) {
            return $service->appointment;
         })
        );

        $appointments = $appointments->merge(
            $gpServices->map(function ($service, $key) {
            return $service->appointment;
         })
        );

        $appointments = $appointments->merge(
            $dentalServices->map(function ($service, $key) {
            return $service->appointment;
         })
        );

        return $this->successfulResponse(200, $appointments);
    }

    /**
     * Store a new appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'medicalScreeningId' => 'required|exists:medical_screenings,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $medicalScreening = MedicalScreening::find($request->medicalScreeningId);
        $appointment = $medicalScreening->appointment()->create([
                'date' => date('d-m-Y',strtotime($request->date)),
                'time' => date('h:i a',strtotime($request->time)),
                'status' => 'BOOKED'
        ]);
        
        $medicalScreening->serviceCenter->notify(new MedicalScreeningAppointment($appointment));
        $medicalScreening->patient->notify(new MedicalScreeningAppointment($appointment));

        return $this->successfulResponse(200, $appointment);
    }

    /**
     * Store an optical service appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOptical(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'opticalServiceId' => 'required|exists:optical_services,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $opticalService = OpticalService::find($request->opticalServiceId);
        $appointment = $opticalService->appointment()->create([
                'date' => date('d-m-Y',strtotime($request->date)),
                'time' => date('h:i a',strtotime($request->time)),
                'status' => 'NEW'
        ]);
        
        $opticalService->serviceCenter->notify(new OpticalServiceRequest($appointment));
       

        return $this->successfulResponse(200, $appointment);
    }

   /**
     * Store a dental service appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDental(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'dentalServiceId' => 'required|exists:dental_services,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $dentalService = DentalService::find($request->dentalServiceId);
        $appointment = $dentalService->appointment()->create([
                'date' => date('d-m-Y',strtotime($request->date)),
                'time' => date('h:i a',strtotime($request->time)),
                'status' => 'NEW'
        ]);
        
        $dentalService->serviceCenter->notify(new DentalServiceRequest($appointment));
       

        return $this->successfulResponse(200, $appointment);
    }

    /**
     * Store a General Practitioner service appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeGp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'gpServiceId' => 'required|exists:general_practitioner_services,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $gpService = GeneralPractitionerService::find($request->gpServiceId);
        $appointment = $gpService->appointment()->create([
                'date' => date('d-m-Y',strtotime($request->date)),
                'time' => date('h:i a',strtotime($request->time)),
                'status' => 'NEW'
        ]);
        
        $gpService->serviceCenter->notify(new GPServiceRequest($appointment));
       

        return $this->successfulResponse(200, $appointment);
    }

    public function storeNurse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
            'bookNurseId' => 'required|exists:book_nurses,id',
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $bookNurse = BookNurse::find($request->bookNurseId);
        $appointment = $bookNurse->appointment()->create([
                'date' => date('d-m-Y',strtotime($request->date)),
                'time' => date('h:i a',strtotime($request->time)),
                'status' => 'NEW'
        ]);
       

        return $this->successfulResponse(200, $appointment);
    }

    public function accept(Appointment $appointment)
    {

        $appointment->update([
            'status' => 'BOOKED'
        ]);

        $patient = $appointment->appointmentable->patient;

        switch ($appointment->appointmentable_type) {
            case 'App\Models\OpticalService':
                $patient->notify(new OpticalServiceRequest($appointment));
                break;

            case 'App\Models\GeneralPractitionerService':
                $patient->notify(new GPServiceRequest($appointment));
                break;

            case 'App\Models\DentalService':
                $patient->notify(new DentalServiceRequest($appointment));
                break;

            default:
                break;
        }

        return $this->successfulResponse(200, $appointment,'Appointment Booking Confirmed!');
    }
    
    public function decline(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'DECLINED'
        ]);

        return $this->successfulResponse(200, $appointment);
    }

    public function book(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'BOOKED'
        ]);
        
        $appointment->appointmentable->serviceCenter->notify(new BookedAppointment($appointment));  
        return $this->successfulResponse(200, $appointment);
    }

    public function show (Appointment $appointment)
    {
       return $this->successfulResponse(200, $appointment);
    }

}
