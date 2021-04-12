<?php

use App\Models\Role;
use App\ServiceContracts\DoctorManagementService;
use App\ServiceContracts\UserManagementService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param DoctorManagementService $doctorManagementService
     * @param UserManagementService $userManagementService
     * @return void
     */
    public function run(DoctorManagementService $doctorManagementService, UserManagementService $userManagementService)
    {
        $doctor  = [
            'first_name' => 'Docky',
            'last_name' => 'Lagbaja',
            'middle_name' => '',
            'email' => 'docky_docky@gmail.com',
            'password' => '123456',
            'gender' => 'MALE',
            'dob' => '2002-08-01',
            'mothers_maiden_name' => 'Iya Agba',
            'mobile_phone_number' => '01234567891',
            'work_phone_number' => '01234567891',
            'mcrn' => '1234sdf',
            'user_role_code' => Role::DOCTOR,
            'year_of_graduation' => '2020-08-31',
            'specialty_code' => 'SPELTY001',
            'school_attended' => 'Yabatech',
            'account_name' => 'Awwal Akanby',
            'account_number' => '1234567890',
            'account_type' => 'CURRENT',
            'bank_code' => 'BK001'
        ];
        $doctorManagementService->createDoctor($doctor);

        $patient = [
            'first_name' => 'Baba',
            'last_name' => 'Muri',
            'middle_name' => 'Abesupinle',
            'email' => 'baba_muri@gmail.com',
            'password' => '123456',
            'gender' => 'MALE',
            'dob' => '2002-08-01',
            'mothers_maiden_name' => 'Iya Agba',
            'mobile_phone_number' => '01234567891',
            'work_phone_number' => '01234567891',
            'mcrn' => '1234sdf',
            'user_role_code' => Role::PATIENT,
        ];

        $userManagementService->createUser($patient);
    }
}
