<?php


namespace App\Repository;

use App\Models\Specialty;
use App\Common\BaseRepository;
use App\Models\DoctorProfile;
use App\Models\Enums\GenericStatusConstant;
use App\Models\User;
use App\RepositoryContracts\DoctorRepository;
use Illuminate\Support\Facades\Storage;

class DoctorRepositoryImpl extends BaseRepository implements DoctorRepository
{
    /**
     * @param array $attributes
     * @return User
     */
    public function save(array $attributes): DoctorProfile
    {
        $attributes = (object)$attributes;

        // $doctorProfile = new DoctorProfile();
        // $doctorProfile->user_id = $attributes->user_id;
        // $doctorProfile->mcrn = $attributes->mcrn;
        // $doctorProfile->year_of_graduation = $attributes->year_of_graduation;
        // $doctorProfile->specialty_id = 1;
        // $doctorProfile->school_attended = $attributes->school_attended;
        // $doctorProfile->save();
        // return $doctorProfile;

         return DoctorProfile::updateOrCreate(
            ['user_id' => auth()->user()->id ],
            ['mcrn' => $attributes->mcrn, 'year_of_graduation' =>$attributes->year_of_graduation,'school_attended' =>$attributes->school_attended, 'account_name' => $attributes->account_name, 'account_number' =>$attributes->account_number,'bank_name' =>$attributes->bank_name, 'working_days' =>$attributes->working_days,'start_time' => $attributes->start_time,'closing_time' => $attributes->closing_time, 'specialty_id' =>Specialty::where('code',$attributes->specialty_code)->first()->id ]
        );
    }

    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return void
     */
    public function getDoctors($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0)
    {
        // TODO: Implement getDoctors() method.
    }

    /**
     * @param string $identifier
     * @param string $status
     * @return object|null|User
     */
    public function getDoctorByIdentifier(string $identifier, $status = GenericStatusConstant::ACTIVE)
    {
        // TODO: Implement getDoctorByIdentifier() method.
    }

    public function getDoctorByEmail(string $email, $status = GenericStatusConstant::ACTIVE, $role = true): User
    {
        // TODO: Implement getDoctorByEmail() method.
    }

    public function storeDoctorCertificate($doctorProfile, $file, $description = null)
    {
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . strtotime(now()) . "." . $file->clientExtension();
        $fileContents = file_get_contents($file->getRealPath());
        Storage::disk('medical_certificates')->put($fileName, $fileContents);
        $doctorProfile->medicalCertificates()->create([
            'file_name' => $fileName
        ]);
    }

    // public function addBranches(DoctorProfile $doctorProfile, array $attributes)
    // {
    //         return $doctorProfile->branches()->create($attributes);
    // }
}
