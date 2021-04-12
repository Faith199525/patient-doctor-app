<?php

namespace App\Http\Controllers;

use App\Models\NutritionChat;
use Illuminate\Http\Request;
use App\Events\NutritionChatSent;
use Validator;

class NutritionChatController extends BaseController
{
    /**
     * Get all Nutrition chats by Nutrition service id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nutritionist_service_id' => 'required|exists:nutritionist_services,id'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $chats = NutritionChat::where('nutritionist_service_id', $request->nutritionist_service_id)->oldest()->get();

        return $this->successfulResponse(200, $chats);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $receiverId)
    {
        $validator = Validator::make($request->all(), [
            'sender' => 'required|exists:users,id',
            'body' => 'required|string',
            'nutritionist_service_id' => 'required|exists:nutritionist_services,id'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $chat = NutritionChat::create($validator->validated());

        broadcast(new NutritionChatSent($chat, $receiverId))->toOthers();
        
        return $this->successfulResponse(200, $chat,'Chat created successfully!');
    }
}
