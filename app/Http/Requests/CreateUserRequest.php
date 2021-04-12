<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'gender' => 'nullable|gender',
            'dob' => 'string|nullable',
            'mothers_maiden_name' => 'string|nullable',
            'mobile_phone_number' => 'nullable|digits:11',
            'work_phone_number' => 'nullable|digits:11',
            'user_role_code' => 'exist_column:roles,code'
        ];
    }

    public function messages()
    {
        return [
            'gender' => 'Gender not valid',
            'user_role_code.exist_column' => 'Please provide a valid user role'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_role_code' => Role::PATIENT,
        ]);
    }
}
