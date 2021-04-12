<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePartnersRequest;
use App\ServiceContracts\PartnersService;
use Illuminate\Http\Request;
use App\Models\Partners;
use App\Models\User;
use Validator;
use DB;

class PartnersController extends BaseController
{
    private $partnersService;

    public function __construct(PartnersService $partnersService)
    {
        $this->partnersService = $partnersService;
    }
    public function createPartner(CreatePartnersRequest $request)
    {

        $attributes = $request->all();

        $result = $this
            ->partnersService
            ->createPartners($attributes);


        return $this->successfulResponse(201, $result);
    }
    /**
     * 
     * @return App\Models\Partner  $partners
    */
    public function index()
    {
        return response()->json(Partners::all(),200);
    }


    /*
     * @params App\Models\Partner  $partners
     * @params Request $request
     * @return 
    */
    public function updatePartner(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //         'name' => 'string',
        //         'address' => 'string',
        //         'license_number' => 'string',
        //         'email' => 'email',
        //         'phone_number' => 'phone',
        //         'description' => 'string',
        //         'type' => 'in:DIAGNOSTIC, AMBULANCE, HOSPITAL, PHARMACY,NURSE,NUTRITIONIST',
        //         'status' => 'in:ACTIVE,IN_ACTIVE,PENDING'
        // ]);

        // if($validator->fails()){
        //      return response()->json(["error" => $validator->messages()], 422);
        // }      
        // $partner->update($validator->validated());

        $user = auth()->user();
        $partnerProfileRequest = ['name','license_number','type','description','working_days','start_time','closing_time','representative_one_name','representative_one_email','representative_one_phone_number','representative_two_name','representative_two_email','representative_two_phone_number','account_name','account_number','bank_name','branches'];

        $partnerProfile = $this->partnersService->saveProfile($request->all());
      
        $user->update($request->except($partnerProfileRequest));
        
        return response()->json([
          "data" => $partnerProfile,
          "message" => "Partner Updated!"
      ], 200);
    }

    /**
     * @param App\Models\Partners $partner
     */
    public function show(Partners $partner)
    {
        return response()->json([
            "data" => $partner
        ], 200);
    }

    /**
     * @param string $type : HOSPITAL, AMBULANCE, DIAGNOSTIC, PHARMACY,NURSE,NUTRITIONIST
     */
    public function getPartnersByType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required','string','regex:(HOSPITAL|AMBULANCE|DIAGNOSTIC|PHARMACY|NURSE|NUTRITIONIST)']
       ]);

        if($validator->fails()){
            return response()->json(["error" => $validator->messages()], 422);
        }

       $partners =  Partners::where('type',$request->type)->get();
       return response()->json([
            "data" => $partners
        ], 200);
    }

    public function showProfile()
    {
        $partner = User::with('partners.branches')->find(auth()->id());

        return $partner;
    }
}
