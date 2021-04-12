<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */

namespace App\Service;

use App\Exceptions\IllegalArgumentException;
use App\Models\RequestedPartner;
use App\Models\User;
//use App\RepositoryContracts\RequestedPartnersRepository;
use App\Repository\RequestedPartnersRepo;
use App\RepositoryContracts\RoleRepository;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\RequestedPartnersService;
use Illuminate\Support\Facades\DB;

class RequestedPartnersServiceImpl implements RequestedPartnersService
{
    private $userRepository;
    private $roleRepository;
    private $requestedPartnersRepository;


    /**
     * UserManagementServiceImpl constructor.
     * @param UserRepository $userRepository
     * @param RequestedPartnersRepository $requestedPartnersRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, RequestedPartnersRepo $requestedPartnersRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->requestedPartnersRepository = $requestedPartnersRepository;
    }

    public function createPartners(array $attributes): User
    {
       
       $userAttributes = collect($attributes['members']);
           
        return DB::transaction(function () use ($userAttributes, $attributes) {

            $role = $this->roleRepository->findRoleByCode($attributes['user_role_code']);

                if (is_null($role)) {
                    throw new IllegalArgumentException('User type does not exist');
                }

                foreach ($userAttributes as $userAttribute){
                $user = $this->userRepository->save($userAttribute);
                $this->roleRepository->assignRoleTo($role->id, $user);
                $this->userRepository->sendVerificationEmail($user->email, $user->email_token);

               // $partner = $this->requestedPartnersRepository->save($attributes, $user);
            }
            return $user;
        });

    }

    public function saveProfile( array $attributes)
    {
        return $this->requestedPartnersRepository->save($attributes);
    }
}
