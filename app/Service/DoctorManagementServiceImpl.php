<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */

namespace App\Service;

use App\Exceptions\IllegalArgumentException;
use App\Models\User;
use App\Models\Specialty;
use App\RepositoryContracts\DoctorRepository;
use App\RepositoryContracts\RoleRepository;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\DoctorManagementService;
use Illuminate\Support\Facades\DB;

class DoctorManagementServiceImpl implements DoctorManagementService
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    private $roleRepository;
    /**
     * @var DoctorRepository
     */
    private $doctorRepository;


    /**
     * UserManagementServiceImpl constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, DoctorRepository $doctorRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->doctorRepository = $doctorRepository;
    }


    /**
     * @param array $attributes
     * @return User
     */
    public function createDoctor(array $attributes): User
    {

        return DB::transaction(function () use ($attributes) {
            $user = $user = $this->userRepository->save($attributes);

            if (array_key_exists('user_role_code', $attributes)) {
                $role = $this->roleRepository->findRoleByCode($attributes['user_role_code']);


                if (is_null($role)) {
                    throw new IllegalArgumentException('User type does not exist');
                }

                $this->roleRepository->assignRoleTo($role->id, $user);
                $this->userRepository->sendVerificationEmail($user->email, $user->email_token);
            }
            return $user;

        });


        // $userAttributes = $this->getUserAttributes($attributes);
        // return DB::transaction(function () use ($attributes, $userAttributes) {
        //     $user = $user = $this->userRepository->save($userAttributes);

        //     if (array_key_exists('user_role_code', $attributes)) {
        //         $role = $this->roleRepository->findRoleByCode($userAttributes['user_role_code']);


        //         if (is_null($role)) {
        //             throw new IllegalArgumentException('User type does not exist');
        //         }

        //         $this->roleRepository->assignRoleTo($role->id, $user);
        //         $this->userRepository->sendVerificationEmail($user->email, $user->email_token);
        //     }

        //     return $user;
        // });



             //$doctorProfile = $this->doctorRepository->save([
              //    'user_id' => $user->id,
            //     'mcrn' => $attributes['mcrn'],
            //     'year_of_graduation' => $attributes['year_of_graduation'],
            //     'specialty_id' => Specialty::where('code',$attributes['specialty_code'])->first()->id,
            //     'school_attended' => $attributes['school_attended']
            // ]);

            // if (array_key_exists('medical_certificate', $attributes)) {
            //     $this->doctorRepository->storeDoctorCertificate($doctorProfile, $attributes['medical_certificate']);
            // }


//            if (array_key_exists('account_number', $attributes)) {
//                $userBank = new UserBankDetails();
//                $userBank->user_id = $user->id;
//                $userBank->account_name = $attributes['account_name'];
//                $userBank->account_number = $attributes['account_number'];
//                $userBank->account_type = $attributes['account_type'];
//                $userBank->bank_id = $attributes['bank_id'];
//
//                $userBank->save();
//            }
        
    }

    private function getUserAttributes($attributes): array
    {
        return [
            'first_name' => $attributes['first_name'] ?? '',
            'last_name' => $attributes['last_name'] ?? '',
            //'middle_name' => $attributes['middle_name'] ?? '',
            'email' => $attributes['email'] ?? '',
            'password' => $attributes['password'] ?? '',
            // 'gender' => $attributes['gender'] ?? '',
            // 'dob' => $attributes['dob'] ?? null,
            // 'mothers_maiden_name' => $attributes['mothers_maiden_name'] ?? '',
            // 'mobile_phone_number' => $attributes['mobile_phone_number'] ?? '',
            // 'work_phone_number' => $attributes['work_phone_number'] ?? '',
            'user_role_code' => $attributes['user_role_code'] ?? ''
        ];
    }

    public function saveProfile( array $attributes)
    {
        $doctorProfile= $this->doctorRepository->save($attributes);
        // $branches = collect($attributes['branches']);

        //        foreach ($branches as $branch){
        //      $this->doctorRepository->addBranches($doctorProfile, $branch);
        //     }

        return $doctorProfile;
    }
}
