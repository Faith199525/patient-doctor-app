<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateRequestedPartnersRequest;
//use App\ServiceContracts\RequestedPartnersService;
use App\Service\RequestedPartnersServiceImpl;
use Illuminate\Http\Request;
use App\Models\RequestedPartner;
use App\Repository\RequestedPartnersRepo;
use App\Models\User;
use Validator;

class RequestedPartnersController extends BaseController
{
    private $requestedPartnersService;

    public function __construct(RequestedPartnersServiceImpl $requestedPartnersService, RequestedPartnersRepo $requestedPartnersRepo)
    {
        $this->requestedPartnersService = $requestedPartnersService;
        $this->requestedPartnersRepo = $requestedPartnersRepo;
    }
    public function createPartner(CreateRequestedPartnersRequest $request)
    {
        $attributes = $request->all();

        $result = $this
            ->requestedPartnersService
            ->createPartners($attributes);


        return $this->successfulResponse(201, $result);
    }
    /**
     * 
     * @return App\Models\Partner  $partners
    */
    public function index()
    {
        return response()->json(RequestedPartner::all(),200);
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

        $user = auth()->user();

        $userProfileRequest = ['license_number','partner_address','registered_name','school_attended','year_of_graduation', 'account_number', 'account_name', 'bank', 'description','certificate','years_of_experience','type'];

        $partnerProfile = $this->requestedPartnersService->saveProfile($request->all());

        if ( $request->certificate != 'undefined' &&  $request->certificate != 'null' ) {
            $this->requestedPartnersRepo->storeCertificate($partnerProfile, $request->certificate);
        }

        // if (isset( $request->certificate)) {
        //         $this->requestedPartnersRepo->storeCertificate($partnerProfile, $request->certificate);
        //     }
            
        $user->update($request->except($userProfileRequest));

        return response()->json([
          "data" => $user,
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
        $partner = User::with('requestedPartner')->find(auth()->id());

        return $partner;
    }
}
