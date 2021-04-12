<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
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
            'password' => 'required|min:6',
            'gender' => 'nullable|string|in:MALE,FEMALE',
            'dob' => 'string|nullable|date_format:Y-m-d|before:' . now()->subYears(18)->format('Y-m-d'),
            'mothers_maiden_name' => 'string|nullable',
            'mobile_phone_number' => 'nullable|digits:11|starts_with:0',
            'work_phone_number' => 'nullable|digits:11|starts_with:0',
            'user_role_code' => 'exist_column:roles,code',
            'mcrn' => 'nullable|string',
            'medical_certificate' => 'nullable|image',
            'year_of_graduation' => 'nullable|date_format:Y|before:today',
            'specialty_code' => 'nullable|string|exists:specialties,code',
            'school_attended' => 'nullable|string',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|digits:10',
            'bank_code' => 'nullable|string|exists:bank_lists,code',
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
            'user_role_code' => Role::DOCTOR,
           // 'account_type' => 'SAVINGS',
        ]);
    }

    public function messages()
    {
        return [
            'starts_with' => 'Field must starts with 0'
        ];
    }
}
