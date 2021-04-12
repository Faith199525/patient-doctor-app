<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequestedPartnersRequest extends FormRequest
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
            'address' => 'nullable',
            'license_number' => 'nullable',
            'registered_name' => 'nullable',
            //'mobile_phone_number' => 'required|digits:11',
            'school_attended' => 'nullable',
            'year_of_graduation' => 'nullable',
            'account_number' => 'nullable',
            'account_name' => 'nullable',
            'account_type' => 'nullable|in:SAVINGS,CURRENT',
            'description' => 'nullable',
            'type' => 'required|in:AMBULANCE,NUTRITIONIST,NURSE',
            //'first_name' => 'required|string',
            //'last_name' => 'required|string',
            //'email' => 'required|email|unique:users',
            //'password' => 'required|min:6|confirmed',
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
            case 'AMBULANCE':
                $role = Role::AMBULANCE;
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
