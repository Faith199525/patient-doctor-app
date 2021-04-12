<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'dob' => 'string|nullable',
            //'middle_name' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'address' => 'nullable|string',
            'kin_first_name' => 'nullable|string',
            'kin_last_name' => 'nullable|string',
            //'kin_country' => 'nullable|string',
            //'kin_state' => 'nullable|string',
            //'kin_city' => 'nullable|string',
            //'kin_address' => 'nullable|string',
            'kin_phone_number' => 'nullable|digits:11',
            'mobile_phone_number' => 'nullable|digits:11',
            
        ];
    }

}
