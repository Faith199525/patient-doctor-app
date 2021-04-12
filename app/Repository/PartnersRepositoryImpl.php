<?php


namespace App\Repository;


use App\Common\BaseRepository;
use App\Models\Enums\GenericStatusConstant;
use App\Models\Partners;
use App\Models\PartnerBranch;
use App\RepositoryContracts\PartnersRepository;
use DB;

class PartnersRepositoryImpl extends BaseRepository implements PartnersRepository
{

    public function save(array $attributes): Partners
    {
         $attributes = (object)$attributes;
         $optionalUser = optional($attributes);
        // $partners = new Partners();
        // $partners->name = $attributes->name;
        // $partners->address = $attributes->address;
        // $partners->license_number = $attributes->license_number;
        // $partners->email = $attributes->email;
        // $partners->phone_number = $attributes->phone_number;
        // $partners->description = $attributes->description;
        // $partners->logo = $attributes->logo ?? null;
        // $partners->type = $attributes->type;

        // $partners->save();
        // return $partners;

        $user = auth()->user();
        $partner=DB::table('partner_members')->where('user_id', $user->id)->pluck('partners_id')->first();
        if ($partner == null){
            // or if(!$partner)
            $partnerUser = new Partners;
            return $partnerUser->create([
                'name' => $optionalUser->name, 'type' => $optionalUser->type, 'address' =>$optionalUser->address,'license_number' =>$optionalUser->license_number, 'email' =>$user->email, 'phone_number' =>$optionalUser->mobile_phone_number,'description' =>$optionalUser->description, 'account_name' => $optionalUser->account_name, 'account_number' =>$optionalUser->account_number,'bank_name' =>$optionalUser->bank_name, 'working_days' =>$optionalUser->working_days,'start_time' => $optionalUser->start_time,'closing_time' => $optionalUser->closing_time, 'representative_one_name' =>$optionalUser->representative_one_name,'representative_one_email' =>$optionalUser->representative_one_email, 'representative_one_phone_number' =>$optionalUser->representative_one_phone_number, 'representative_two_name' =>$optionalUser->representative_two_name,'representative_two_email' =>$optionalUser->representative_two_email, 'representative_two_phone_number' => $optionalUser->representative_two_phone_number, 'logo' =>$optionalUser->logo ?? null,  ]
        );
        }
        $partnerId= Partners::find($partner);
          $partnerId->update([
                'name' => $optionalUser->name, 'type' => $optionalUser->type, 'address' =>$optionalUser->address,'license_number' =>$optionalUser->license_number, 'email' =>$user->email, 'phone_number' =>$optionalUser->mobile_phone_number,'description' =>$optionalUser->description, 'account_name' => $optionalUser->account_name, 'account_number' =>$optionalUser->account_number,'bank_name' =>$optionalUser->bank_name, 'working_days' =>$optionalUser->working_days,'start_time' => $optionalUser->start_time,'closing_time' => $optionalUser->closing_time, 'representative_one_name' =>$optionalUser->representative_one_name,'representative_one_email' =>$optionalUser->representative_one_email, 'representative_one_phone_number' =>$optionalUser->representative_one_phone_number, 'representative_two_name' =>$optionalUser->representative_two_name,'representative_two_email' =>$optionalUser->representative_two_email, 'representative_two_phone_number' => $optionalUser->representative_two_phone_number, 'logo' =>$optionalUser->logo ?? null,  ]
        );
         return $partnerId;
    }

    public function getAllPartners($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0)
    {
        // TODO: Implement getAllPartners() method.
    }

    public function getPartner($id, $status = GenericStatusConstant::ACTIVE): Partners
    {
        return Partners::where('id', $id)->first();
    }

    public function getMembers(Partners $partners)
    {
        // TODO: Implement getMembers() method.
    }

    public function addMembers(Partners $partners, $user)
    {
        $partners->members()->syncWithoutDetaching($user->id);
    }

    public function addBranches(Partners $partners, array $attributes)
    {
        // $branch=DB::table('partner_branches')->where('partners_id', $partners->id)->first();
        // if ($branch == null){
        //     // or if(!$partner)
        //     return $partners->branches()->create($attributes);
        // }

        // $attributes = (object)$attributes;
        // return $partners->branches()->update([
        //     'address' => $attributes->address, ''
        // ]);
        
        // $attributes = (object)$attributes;
        // $optionalUser = optional($attributes);
        // return PartnerBranch::updateOrCreate(
        //     ['partners_id' => $partners->id ],
        //     ['address' => $optionalUser->address, 'email' =>$optionalUser->email, 'phone_number' => $optionalUser->phone_number]
        // );

        
            
    }
}
