<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\ServiceContracts\UserManagementService;

class SendUserVerificationNotification
{

    /**
     * @var UserManagementService
     */
    private $userManagementService;


    /**
     * SendUserVerificationNotification constructor.
     * @param UserManagementService $userManagementService
     */
    public function __construct(UserManagementService $userManagementService)
    {

        $this->userManagementService = $userManagementService;
    }


    /**
     * @param UserRegisteredEvent $userRegisteredEvent
     */
    public function handle(UserRegisteredEvent $userRegisteredEvent)
    {

        $refreshToken = $this->userManagementService->generateUserRefreshToken($userRegisteredEvent->user);
        $verificationUrl = env('APP_URL') . '/email-verification/' . $refreshToken;

    }
}
