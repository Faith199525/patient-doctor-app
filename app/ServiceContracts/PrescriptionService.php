<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */
namespace App\ServiceContracts;


use App\Models\Prescription;

interface PrescriptionService
{
    /**
     * @param array $attributes
     * @return Prescription
     */
    public function createPrescription(array $attributes): Prescription;

    //public function addDrugToPrescription(Prescription $prescription, array $items);

    public function addDrugToPrescription(Prescription $prescription, array $attributes);

    public function attachPharmacyToPrescription(Prescription $prescription, $user);

    public function updatePrescription(Prescription $prescription, array $attributes);

}
