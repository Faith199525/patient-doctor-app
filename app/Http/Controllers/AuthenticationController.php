<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserDetailsResource;
use App\Models\Enums\GenericStatusConstant;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\UserManagementService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /**
     * @var UserManagementService
     */
    private $userManagementService;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository,
                                UserManagementService $userManagementService)
    {
        $this->userRepository = $userRepository;
        $this->userManagementService = $userManagementService;
    }

    public function login(LoginRequest $request)
    {
        $token = null;

        try {
            $token = auth()->attempt($request->all(), true);
        } catch (\Exception $ex) {
            throw new AuthenticationException('Provided credentials is not valid. ' . $ex->getMessage());
        }

        if ($token) {
            $user = $this->userRepository->getUserByEmail($request->email, GenericStatusConstant::ACTIVE, true);
            $token = $user->createToken('authToken')->accessToken;
            $userDetailsResource = new UserDetailsResource($user);
            $userDetailsResource->setToken($token);
            return $userDetailsResource;
        }
        return response()->json( ['message'=>'user name or password is invalid'], 401);
    }
}
