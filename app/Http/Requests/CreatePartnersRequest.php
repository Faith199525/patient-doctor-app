<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreatePartnersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable',
            'address' => 'nullable',
            'license_number' => 'nullable',
            'email' => 'nullable',
            'phone_number' => 'nullable',
            'description' => 'nullable',
            'type' => 'required|in:DIAGNOSTIC,HOSPITAL,PHARMACY',
            'members' => 'array|required',
            'members.*.first_name' => 'required|string',
            'members.*.last_name' => 'required|string',
            'members.*.email' => 'required|email|unique:users',
            'members.*.password' => 'required|min:6|confirmed',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $role = $this->getRoleForPartnerType();
        $this->merge([
            'user_role_code' => $role,
        ]);
    }

    protected function getRoleForPartnerType(): string
    {
        $role = '';
        switch ($this->request->get('type')) {
            case 'DIAGNOSTIC':
                $role = Role::DIAGNOSTIC;
                break;
            case 'AMBULANCE':
                $role = Role::AMBULANCE;
                break;
            case 'HOSPITAL':
                $role = Role::HOSPITAL;
                break;
            case 'PHARMACY':
                $role = Role::PHARMACY;
            break;
            case 'NURSE':
                $role = Role::NURSE;
            break;
            case 'NUTRITIONIST':
                $role = Role::NUTRITIONIST;
        }
        return $role;
    }
}
