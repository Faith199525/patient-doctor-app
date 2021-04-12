<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string|null first_name
 * @property string|null last_name
 * @property string|null middle_name
 * @property string|null email
 * @property string|null gender
 *@property mixed|string password
 * @property mixed|null mothers_maiden_name
 * @property mixed|null mobile_phone_number
 * @property mixed|null work_phone_number
 * @property mixed|string status
 * @property mixed|string dob
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Appends custom properties to the user object
     *
     * @var array
     */
    protected $appends = [ 'profile_picture' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['medicalHistory'];

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'User.'.$this->id;
    }


    public function getProfilePictureAttribute()
    {
        $profile_picture = $this->files()->first();
        if ($profile_picture) {

         if(env('APP_ENV') == 'production'){
            $profile_picture_path = url('/api/v1/user/profile_image/'. $this->id );
            return $profile_picture_path;
     
         }
            $profile_picture_path = url('/api/'. config("app.api_version") .'/user/profile_image/'. $this->id );
            return $profile_picture_path;
           // return response()->file(storage_path("app/{$profile_image->path}"));
        }else {
            return "no_image";
        }
    }


    public function passwordReset()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function referrals()
    {
       return $this->hasMany('App\Models\Referral');
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }

    public function requestedPartner()
    {
       return $this->hasOne('App\Models\RequestedPartner');
    }

    public function partners()
    {
        return $this->belongsToMany(Partners::class, 'partner_members', 'user_id', 'partners_id');
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }

    public function scopeHospital($query)
    {
        return $query->whereHas('partners', function ($query) {
            $query->where('type', Partners::HOSPITAL);
        });
    }

    public function files()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }

    public function callup()
    {
       return $this->hasMany('App\Models\Callup', 'user_id');
    }

    public function ambulance()
    {
       return $this->hasMany('App\Models\Callup', 'ambulance_id');
    }

    public function bookNurse()
    {
       return $this->hasMany('App\Models\BookNurse','patient_id');
    }

    public function nurse()
    {
       return $this->hasMany('App\Models\BookNurse','partner_id');
    }

    public function caseFiles()
    {
        return $this->hasMany('App\Models\CaseFile','patient_id');
    }

    public function medicalHistory()
    {
       return $this->hasOne('App\Models\MedicalHistory','patient_id');
    }

    public function doctorProfile()
    {
       return $this->hasOne('App\Models\DoctorProfile');
    }

    public function medicalScreeningTests()
    {
       return $this->hasMany('App\Models\MedicalScreening','patient_id');
    }

    public function medicalScreening()
    {
       return $this->hasMany('App\Models\MedicalScreening','diagnostic_id');
    }

    public function opticalServiceRequest()
    {
       return $this->hasMany('App\Models\OpticalService','patient_id');
    }

    public function opticalService()
    {
       return $this->hasMany('App\Models\OpticalService','optician_id');
    }

    public function gpServiceRequest()
    {
       return $this->hasMany('App\Models\GeneralPractitionerService','patient_id');
    }

    public function gpService()
    {
       return $this->hasMany('App\Models\GeneralPractitionerService','gp_id');
    }

    public function diagnosis()
    {
       return $this->hasMany('App\Models\Diagnosis','partners_id');
    }
    public function dentalServiceRequest()
    {
       return $this->hasMany('App\Models\DentalService','patient_id');
    }

    public function dentalService()
    {
       return $this->hasMany('App\Models\DentalService','dentist_id');
    }

    public function nutritionistServiceRequest()
    {
       return $this->hasMany('App\Models\NutritionistService','patient_id');
    }

    public function nutritionistService()
    {
       return $this->hasMany('App\Models\NutritionistService','nutritionist_id');
    }

}
