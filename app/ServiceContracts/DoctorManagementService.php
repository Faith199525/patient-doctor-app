<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */
namespace App\ServiceContracts;


use App\Exceptions\IllegalArgumentException;
use App\Models\User;

interface DoctorManagementService
{

    /**
     * @param array $attributes
     * @return User
     */
    public function createDoctor(array $attributes): User;

    public function saveProfile(array $attributes);

}
