<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */

namespace App\Service;

use App\Exceptions\IllegalArgumentException;
use App\Models\Partners;
use App\Models\User;
use App\RepositoryContracts\PartnersRepository;
use App\RepositoryContracts\RoleRepository;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\PartnersService;
use Illuminate\Support\Facades\DB;
use App\Models\PartnerBranch;

class PartnersServiceImpl implements PartnersService
{
    private $userRepository;
    private $roleRepository;
    private $partnersRepository;


    /**
     * UserManagementServiceImpl constructor.
     * @param UserRepository $userRepository
     * @param PartnersRepository $partnersRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, PartnersRepository $partnersRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->partnersRepository = $partnersRepository;
    }

    public function createPartners(array $attributes): User
    {

        $userAttributes = collect($attributes['members']);
        return DB::transaction(function () use ($userAttributes, $attributes) {
           // $partner = $this->partnersRepository->save($attributes);

            $role = $this->roleRepository->findRoleByCode($attributes['user_role_code']);

                if (is_null($role)) {
                    throw new IllegalArgumentException('User type does not exist');
                }

            foreach ($userAttributes as $userAttribute){
                $user = $this->userRepository->save($userAttribute);
                //$this->partnersRepository->addMembers($partner, $user);
                $this->roleRepository->assignRoleTo($role->id, $user);
                $this->userRepository->sendVerificationEmail($user->email, $user->email_token);
            }
             return $user;
            // return $partner->with('members')->first();
        }, 5);

    }

     public function saveProfile( array $attributes)
    {
        $user = auth()->user();
        $partner= $this->partnersRepository->save($attributes);

        if(DB::table('partner_members')->where('user_id', $user->id)->doesntexist()){

           $this->partnersRepository->addMembers($partner, $user);
        }       

        $branches = collect($attributes['branches']);

            foreach ($branches as $branch){
             //$this->partnersRepository->addBranches($partner, $branch);
             $partnerbranch = PartnerBranch::where('partners_id', $partner->id)->first();
             if($partnerbranch == null){
                $partner->branches()->create($branch);
             }
              else if(isset($branch['id'])){
                 $branchExists= PartnerBranch::find($branch['id']);
                 $branchExists->update($branch);
             }
             else {
                 $partner->branches()->create($branch);
             }
           
            }

        return $partner;
//$ao =DB::table('partner_members')->where('user_id', $user->id)->doesntexist();

      
    }
}
