<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed user
 * @property mixed roles
 */
class UserDetailsResource extends JsonResource
{

    /**
     * @var mixed
     */
    private $token;

    public function setToken($token)
    {
        $this->token = $token;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $token = [];
        if (isset($this->token)) {
            $token = [
                "token" => $this->token
            ];
        }
        return $response = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'profile_picture' => $this->profile_picture,
            'email' => $this->email,
            'mobile_phone_number' => $this->mobile_phone_number,
            'work_phone_number' => $this->work_phone_number,
            'status' => $this->status,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'verified' => $this->verified,
            'subscription' => $this->subscriptions()->where('active',true)->first() ? 1 : 0,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'partners' => PartnersResource::collection($this->whenLoaded('partners')),
            'token' => $token
        ];
//        return array_merge($response, $token);
    }
}
