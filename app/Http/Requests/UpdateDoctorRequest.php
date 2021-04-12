<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('doctor');
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
            //'middle_name' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'address' => 'nullable|string',
            'working_days' => 'nullable|string',
            'working_time' => 'nullable|string',
            // 'rep_one_name' => 'nullable|string',
            // 'rep_one_email' => 'nullable|email',
            // 'rep_one_phone_number' => 'nullable|string',
            // 'rep_two_name' => 'nullable|string',
            // 'rep_two_email' => 'nullable|email',
            // 'rep_two_phone_number' => 'nullable|string',
            'medical_certificate' => 'nullable',
            'work_phone_number' => 'nullable|numeric',
            'mobile_phone_number' => 'nullable|numeric',
            'mcrn' => 'nullable|string',
            'year_of_graduation' => 'date_format:Y|before:today',
            'specialty_code' => 'string|exists:specialties,code',
            'school_attended' => 'nullable|string',
            'account_type' => 'nullable|string',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|numeric',
            // 'bank_code' => 'nullable|string|exists:bank_lists,code',
            'bank_name'=> 'nullable|string',
        ];
    }
}
