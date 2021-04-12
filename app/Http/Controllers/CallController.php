<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MakeCall;
use App\Events\EndCall;
use App\Events\DeclineCall;


class CallController extends BaseController
{
    public function call($id)
    {
        broadcast(new MakeCall($id))->toOthers();
    }

    public function endCall($id)
    {
        broadcast(new EndCall($id))->toOthers();
    }

    public function declineCall($id)
    {
        broadcast(new DeclineCall($id))->toOthers();
    }
    
}
