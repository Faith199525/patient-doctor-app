<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class MessageController extends BaseController
{
    public function send(CaseFile $caseFile, Request $request)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        $receiverId = auth()->id() == $caseFile->patient_id ? $caseFile->doctor_id : $caseFile->patient_id;
            $message = Message::create([
                'case_id' => $caseFile->id,
                'body' => $request->get('body'),
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
            ]);
        return $this->successfulResponse(201, $message, 'success');
    }


    /**
     * Return message list for a case
     * @return JsonResponse
     */

    public function receive($caseFile)
    {
        $messages = Message::whereCaseId($caseFile)->get();
        return $this->successfulResponse(200, $messages, 'success');
    }

    public function sendMessageToUser(Request $request,User $user)
    {
        $request->validate([
            'body' => 'required|string'
        ]);
        
        if (auth()->id() == $user->id){
            return $this->failedResponse('Cannot send message to self', 422);
        }

        $message = Message::create([
            'body' => $request->body,
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
        ]);

        $receiver = $user;
        $sender = auth()->user();
        
        broadcast(new MessageSent($sender,$receiver,$message))->toOthers();
        return $this->successfulResponse(201, $message, 'Success');
    }


    /**
     * 
     * @return JsonResponse
     */

    public function receiveMessageFromUser(User $user)
    {
        $messages = Message::where('receiver_id',auth()->id())->where('sender_id',$user->id)->get();
        return $this->successfulResponse(200, $messages, 'success');
    }
}
