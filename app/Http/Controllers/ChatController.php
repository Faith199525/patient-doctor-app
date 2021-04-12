<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Events\ChatSent;
use Validator;

class ChatController extends BaseController
{
    /**
     * Get all chats by Case Id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'case_id' => 'required|exists:case_files,id'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $chats = Chat::where('case_id', $request->case_id)->oldest()->get();

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
            'case_id' => 'required|exists:case_files,id'
        ]);

        if($validator->fails()){
            return $this->failedResponse($validator->messages(), 422);
        }

        $chat = Chat::create($validator->validated());

        broadcast(new ChatSent($chat, $receiverId))->toOthers();
        
        return $this->successfulResponse(200, $chat,'Chat created successfully!');

    }
}
