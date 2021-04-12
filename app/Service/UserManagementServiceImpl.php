<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */

namespace App\Service;


//use App\Events\UserRegisteredEvent;
use App\Exceptions\IllegalArgumentException;
use App\Models\PasswordReset;
use App\Models\User;
use App\RepositoryContracts\RoleRepository;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\UserManagementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementServiceImpl implements UserManagementService
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    private $roleRepository;


    /**
     * UserManagementServiceImpl constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }


    /**
     * @param array $attributes
     * @return User
     */
    public function createUser(array $attributes): User
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
    }

    public function updateUser(User $user, array $attributes)
    {
        return $this->userRepository->update($user, $attributes);
    }


    public function doPasswordReset(string $token, string $newPassword): bool
    {
        $email = $this->validateUserDefinedToken($token);

        $user = $this->userRepository->getUserByEmail($email);
        $user->password = Hash::make($newPassword);

        return $user->save();
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function deactivateUser(string $identifier): bool
    {
        // TODO: Implement deactivateUser() method.
    }

    /**
     * @param $token
     * @return bool
     */
    public function validateEmail($token): bool
    {
        // TODO: Implement validateEmail() method.
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateUserRefreshToken(User $user): string
    {



    }

    private function validateUserDefinedToken(string $token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            abort(500, 'The password reset token is invalid.');
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            abort(500, 'The password reset token is invalid.');
        }
        $email = $passwordReset->email;
        $passwordReset->delete();
        return $email;
    }
}
