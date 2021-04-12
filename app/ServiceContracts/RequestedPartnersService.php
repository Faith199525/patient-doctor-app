<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */


namespace App\ServiceContracts;


use App\Models\RequestedPartner;
use App\Models\User;

interface RequestedPartnersService
{

    /**
     * @param array $attributes
     * @return Partners
     */
    public function createPartners(array $attributes): User;

    public function saveProfile(array $attributes);

}
