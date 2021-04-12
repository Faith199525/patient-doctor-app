<?php

namespace App\Listeners;

use App\Events\PasswordResetEvent;
use App\Mail\SendPasswordReset;
use App\ServiceContracts\UserManagementService;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail
{
    /**
     * @var UserManagementService
     */
    private $userManagementService;


    /**
     * SendPasswordResetEmail constructor.
     * @param UserManagementService $userManagementService
     */
    public function __construct(UserManagementService $userManagementService)
    {

        $this->userManagementService = $userManagementService;
    }


    /**
     * @param PasswordResetEvent $event
     */
    public function handle(PasswordResetEvent $event)
    {
        Mail::to($event->user->email)->send(new SendPasswordReset($event));
    }
}
