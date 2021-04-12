<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 28/05/2020
 * Time: 10:36 AM
 */


namespace App\RepositoryContracts;


use App\Models\Prescription;
use App\Common\Contracts\BaseRepositoryContract;

interface PrescriptionRepository extends BaseRepositoryContract
{
    public function createPrescription(array $attributes);

    public function updatePrescription(Prescription $prescription, array $attributes);

    public function addDrugToPrescription(Prescription $prescription, array $attributes);

    public function getPrescription($id): Prescription;
}
