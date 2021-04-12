<?php


namespace App\Repository;


use App\Common\BaseRepository;
use App\Models\Enums\GenericStatusConstant;
use App\Models\RequestedPartner;
use App\RepositoryContracts\RequestedPartnersRepository;
use Illuminate\Support\Facades\Storage;

//class RequestedPartnersRepositoryImpl extends BaseRepository implements RequestedPartnersRepository
class RequestedPartnersRepo extends BaseRepository 
{

    public function save(array $attributes): RequestedPartner
    {
         $attributes = (object)$attributes;
         $optionalUser = optional($attributes);
        // $partners = new RequestedPartner();
        // $partners->account_name = $attributes->account_name;
        // $partners->address = $attributes->address ?? null;
        // $partners->license_number = $attributes->license_number;
        // $partners->account_number = $attributes->account_number;
        // $partners->account_type = $attributes->account_type;
        // $partners->description = $attributes->description ?? null;
        // $partners->year_of_graduation = $attributes->year_of_graduation ?? null;
        // $partners->type = $attributes->type;
        // $partners->school_attended = $attributes->school_attended ?? null;
        // $partners->registered_name = $attributes->registered_name ?? null;
        // $partners->bank= $attributes->bank;
        // $partners->user()->associate($user);

        // $partners->save();
        // return $partners;

        return RequestedPartner::updateOrCreate(
            ['user_id' => auth()->user()->id ],
            ['account_name' => $optionalUser->account_name,'address' => $optionalUser->partner_address, 'license_number' =>$optionalUser->license_number,'type' =>$optionalUser->type, 'account_number' =>$optionalUser->account_number, 'account_type' => $optionalUser->account_type, 'description' =>$optionalUser->description,'year_of_graduation' =>$optionalUser->year_of_graduation, 'school_attended' =>$optionalUser->school_attended, 'registered_name' =>$optionalUser->registered_name, 'bank' => $optionalUser->bank, 'years_of_experience' =>$optionalUser->years_of_experience ]
        );
    }

    public function storeCertificate($partnerProfile, $file, $description = null)
    {
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . strtotime(now()) . "." . $file->clientExtension();
        $fileContents = file_get_contents($file->getRealPath());
        Storage::disk('requested_partners_certificates')->put($fileName, $fileContents);
        $partnerProfile->certificates()->create([
            'file_name' => $fileName
        ]);
    }


}
