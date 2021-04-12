<?php


namespace App\Repository;


use App\Common\BaseRepository;
use App\Models\Enums\GenericStatusConstant;
use App\Models\PasswordReset;
use App\Models\User;
use App\RepositoryContracts\UserRepository;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Carbon\Carbon;
use Dlabs\PaginateApi\PaginateApiAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Integer;

class UserRepositoryImpl extends BaseRepository implements UserRepository
{
    /**
     * UserRepositoryImpl constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param string $email
     * @param string $status
     * @param bool $role
     * @return Builder|Model|User
     */
    public function getUserByEmail(string $email, $status = GenericStatusConstant::ACTIVE, $role = true): User
    {
        return $this->with([$role? 'roles' : ''])
            ->where('status', $status)
            ->where('users.email', $email)
            ->firstOrFail(['users.*']);
    }

    /**
     * @param integer $id
     * @param string $status
     * @param bool $role
     * @return Builder|Model|User
     */
    public function getUserById($id, $status = GenericStatusConstant::ACTIVE, $role = true): User
    {
        return $this->with([$role? 'roles' : ''])
            ->where('status', $status)
            ->where('users.id', $id)
            ->firstOrFail(['users.*']);
    }

    /**
     * @param array $attributes
     * @return User
     */
    public function save(array $attributes): User
    {
        $userAttribute = (object)$attributes;
        $optionalUser = optional($userAttribute);
        $validateEmail = $optionalUser->validateEmail ?? false;
        $status = !$validateEmail ? GenericStatusConstant::ACTIVE : GenericStatusConstant::PENDING;
        $user = new User();
        $user->first_name = $optionalUser->first_name;
        $user->last_name = $optionalUser->last_name;
        $user->middle_name = $optionalUser->middle_name;
        $user->email = $optionalUser->email;
        $user->password = Hash::make($optionalUser->password);
        $user->gender = $optionalUser->gender;
        $user->mothers_maiden_name = $optionalUser->mothers_maiden_name;
        $user->mobile_phone_number = $optionalUser->mobile_phone_number;
        $user->work_phone_number = $optionalUser->work_phone_number;
        $user->status = $status;
        $user->email_token = $this->generate_random_token(80);

        optional($optionalUser->dob, function ($dob) use ($user) {
            $user->dob = Carbon::createFromFormat('Y-m-d', $dob)->startOfDay()->toDateTimeString();
        });
        $user->save();
        return $user;
    }

    public function generate_random_token(int $length){
        return Str::random($length);
    }

    public function update(User $user, array $attributes)
    {
        $userAttribute = (object)$attributes;
        $optionalUser = optional($userAttribute);

        $user->update([
            'first_name' => $optionalUser->first_name, 
            'last_name' => $optionalUser->last_name,
            'dob' => $optionalUser->dob,
            //'middle_name' => $optionalUser->middle_name,
            'mobile_phone_number' => $optionalUser->mobile_phone_number,
            'address' => $optionalUser->address,
            'country' => $optionalUser->country,
            'state' => $optionalUser->state,
            'city' => $optionalUser->city,
            'kin_first_name' => $optionalUser->kin_first_name,
            'kin_phone_number' => $optionalUser->kin_phone_number,
            'kin_last_name' => $optionalUser->kin_last_name]);
           // 'kin_address' => $optionalUser->kin_address,
           // 'kin_country' => $optionalUser->kin_country,
           // 'kin_state' => $optionalUser->kin_state,
           // 'kin_city' => $optionalUser->kin_city]);

          return $user;
        
    }

    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return PaginateApiAwarePaginator
     */
    public function getUsers($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0)
    {
        return $this->userInBuilder($status)
            ->paginate($limit, $offset, [
                'users.*'
            ]);
    }

    public function sendVerificationEmail($email, $token){
        $domain_name = config('credentials.domain_url');
        $link = $domain_name."/auth/verify/".$token;
        Mail::to($email)->send(new EmailVerificationMail($link));
    }

    public function verifyUser($token){
        $token = User::where('email_token',$token)->first();
        return $token ? true : false;
    }



    /**
     * @param string $identifier
     * @param string $status
     * @return object|null|User
     */
    public function getUserByIdentifier(string $identifier, $status = GenericStatusConstant::ACTIVE)
    {


        return $this
            ->userInBuilder($status)
            ->where('users.identifier', $identifier)
            ->firstOrFail([
                'users.*'
            ]);
    }


    /**
     * @param $status
     * @return UserRepositoryImpl
     */
    private function userInBuilder($status): UserRepositoryImpl
    {
        return $this->join('memberships', 'memberships.user_id', 'users.id')
            ->join('portal_accounts', 'portal_accounts.id', 'memberships.portal_account_id')
            ->where('users.status', $status)
            ->where('memberships.status', 'ACTIVE')
            ->where('portal_accounts.status', 'ACTIVE');
    }

    public function generateUserRefreshToken($email)
    {
        $user = $this->getUserByEmail($email);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Str::random(60)
            ]
        );
        return (object)compact('user', 'passwordReset');
    }
}
