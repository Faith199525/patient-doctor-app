<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\MedicalScreening;
use App\Models\Diagnosis;
use App\Models\Prescription;
use App\Models\BookNurse;
use App\Models\OpticalService;
use App\Models\GeneralPractitionerService;
use App\Models\Subscription;
use App\Models\DentalService;
use App\Models\CaseFile; 
use App\Models\NutritionistService;
use App\Events\PaymentMade;

class PaymentController extends BaseController
{

    /**
     * 
     * Save payment Details
     */

     public function store(Request $request)
     {
         switch (true) {
             case $request->has('medicalScreeningId'):
                 $service = MedicalScreening::find($request->medicalScreeningId);
                 break;

            case $request->has('opticalServiceId'):
                 $service = OpticalService::find($request->opticalServiceId);
                 break;

            case $request->has('gpServiceId'):
                 $service = GeneralPractitionerService::find($request->gpServiceId);
                 break;

            case $request->has('dentalServiceId'):
                 $service = DentalService::find($request->dentalServiceId);
                 break;

            case $request->has('caseId'):
                 $service = CaseFile::find($request->caseId);
                 break;

            case $request->has('nutritionistServiceId'):
                 $service = NutritionistService::find($request->nutritionistServiceId);
                 break;
                 
             
             default:
                 # code...
                 break;
         }
         
         if(!$service){
            return $this->failedResponse('Cannot assign payment to null entity: Define service owning payment', 422);
         }
         
         $payment = $service->payment()->create([
            'payment_details' => $request->payment,
        ]);

        event(new PaymentMade($payment));

        return $this->successfulResponse(200, $payment);
     }

    public function verifyPayment(Request $request)
    {
        $paymentDetails = Payment::find($request->paymentId);
                                
        if (!$paymentDetails || $paymentDetails->status == 'FAILED') {
            return response()->json(['data' => 'failed'], 200);
        }                       
        
        return response()->json(['data' => 'confirmed'], 200);
    }

    public function payForDrugsResponse(Request $request)
    {
        $user_id = $request->user_id;
        $prescription_id = $request->prescriptionId;
        $paymentDetail= $request->paymentResponse;
        $amount= $request->amount;
        $total= $request->total;
        $discount= $request->discount;
        $bonusDiscount= $request->bonusDiscount;

        $prescription = Prescription::find($request->prescriptionId);

        //$data = array( $paymentDetail, 'prescription_id'=> $prescription_id, 'amount' =>$amount, 'drugsTotalAmount'=> $total, 'basic5%dicount'=> $discount, 'bonusDiscountForPrenium'=> $bonusDiscount);

        $data= collect($paymentDetail);
        $data->put('amount', $amount);
        $data->put('drugsTotalBeforeAnyDiscount', $total);
        $data->put('basicDiscount', $discount);
        $data->put('bonusDiscountForPrenuimSubscribers', $bonusDiscount);

        $payment = $prescription->payment()->create([
            'payment_details' => $data,
        ]);

        event(new PaymentMade($payment));

        return $this->successfulResponse(200, $payment);
    }

    public function payForTestsResponse(Request $request)
    {
        $user_id = $request->user_id;
        $diagnosis_id = $request->diagnosisId;
        $paymentDetail= $request->paymentResponse;
        $amount= $request->amount;

        $diagnosis = Diagnosis::find($request->diagnosisId);

       // $data = array('callbackResponse' => $paymentDetail, 'diagnosis_id'=> $diagnosis_id,'amount' =>$amount, 'user_id' => $user_id);
         
        $data= collect($paymentDetail);
        $data->put('amount', $amount);

        $payment = $diagnosis->payment()->create([
            'payment_details' => $data,
        ]);

        event(new PaymentMade($payment));

        return $this->successfulResponse(200, $payment);
    }

    public function bookNurse(Request $request)
    {
        $user_id = $request->user_id;
        $booking_id = $request->booking_id;
        $paymentDetail= $request->paymentResponse;
        $amount= $request->amount;

        $booking = BookNurse::find($request->booking_id);

       // $data = array('callbackResponse' => $paymentDetail, 'booking_id'=> $booking_id,'amount' =>$amount, 'user_id' => $user_id);
         
        $data= collect($paymentDetail);
        $data->put('amount', $amount);

        $payment = $booking->payment()->create([
            'payment_details' => $data,
        ]);

        event(new PaymentMade($payment));

        return $this->successfulResponse(200, $payment);
    }

    public function subscriptionPayment(Request $request)
    {
        $payment= $request->reference;
        $amount= $request->amount;
        $subscriptionId= $request->subscriptionId;

        $sub = Subscription::find($request->subscriptionId);

        //$data = array('reference' => $payment, 'amount' =>$amount);
        $data= collect($payment);
        $data->put('amount', $amount);

        $payment = $sub->payment()->create([
            'payment_details' => $data,
        ]);

        event(new PaymentMade($payment));

        return $this->successfulResponse(200, $payment);
    }
}