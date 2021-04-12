<?php

namespace App\Http\Controllers;

use App\Models\NutritionistService;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Events\NutritionistServiceAccepted;
use App\Notifications\NutritionistServiceRequest;

class NutritionistServiceController extends BaseController
{
    /**
     * Display a listing of all Nutritionists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nutritionists = User::role('nutritionist')->get();

        return $this->successfulResponse(200, $nutritionists);
    }

     /**
     * Display a listing of Nutrition services for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function nutritionistServices()
    {
        $user = auth()->user();

        if($user->hasRole('nutritionist')){
          $nutritionistServices = $user->nutritionistService;
        } else {
          $nutritionistServices = $user->nutritionistServiceRequest;
        }

        return $this->successfulResponse(200, $nutritionistServices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'initial_complain' => 'required|string',
            'nutritionist_id' => 'required|exists:users,id'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $nutritionistService = $request->user()->nutritionistServiceRequest()->create($validator->validated());
           
        $nutritionistService->nutritionist->notify(new NutritionistServiceRequest($nutritionistService));
        

        return $this->successfulResponse(201, $nutritionistService, 'Nutritionist Service Request created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NutritionistService  $nutritionistService
     * @return \Illuminate\Http\Response
     */
    public function show(NutritionistService $nutritionistService)
    {
        return $this->successfulResponse(200, $nutritionistService);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\NutritionistService  $nutritionistService
     * @return \Illuminate\Http\Response
     */
    public function accept(NutritionistService $nutritionistService)
    {
        $nutritionistService->update(['status' => 'ACTIVE']);

        broadcast(new NutritionistServiceAccepted($nutritionistService))->toOthers();

        return $this->successfulResponse('200', $nutritionistService, 'Nutritionist Service Request accepted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NutritionistService  $nutritionistService
     * @return \Illuminate\Http\Response
     */
    public function destroy(NutritionistService $nutritionistService)
    {
        //
    }
}
