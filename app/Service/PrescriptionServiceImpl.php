<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 27/05/2020
 * Time: 9:32 PM
 */

namespace App\Service;

use App\Exceptions\IllegalArgumentException;
use App\Models\Prescription;
use App\RepositoryContracts\PrescriptionRepository;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\PrescriptionService;

class PrescriptionServiceImpl implements PrescriptionService
{

    /**
     * @var PrescriptionRepository
     */
    private $prescriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(PrescriptionRepository $prescriptionRepository, UserRepository $userRepository)
    {
        $this->prescriptionRepository = $prescriptionRepository;
        $this->userRepository = $userRepository;
    }

    public function createPrescription(array $attributes): Prescription
    {
        //return $this->prescriptionRepository->createPrescription($attributes);
       $prescription = $this->prescriptionRepository->createPrescription($attributes);
        //$this->prescriptionRepository->addDrugToPrescription($prescription, $drugs);
       $prescribedDrugs = collect($attributes['drugs']);

               foreach ($prescribedDrugs as $prescribedDrug){
               $drugs = $this->prescriptionRepository->addDrugToPrescription($prescription, $prescribedDrug);
            }

            return $prescription; 
    }

    public function addDrugToPrescription(Prescription $prescription, array $item)
    {
       /* return $this->prescriptionRepository->addDrugToPrescription($prescription, $item);

        /*$prescribedDrugs = collect($item['drugs']);


            foreach ($prescribedDrugs as $prescribedDrug){
                $drugs = $this->prescriptionRepository->addDrugToPrescription($prescription, $prescribedDrug);
            }

            return $drugs;     */ 
    } 

    public function attachPharmacyToPrescription(Prescription $prescription, $attribute)
    {
        $user = $this->userRepository->getUserById($attribute['partners_id']);
        if (! $user->partners){
            throw new IllegalArgumentException('User is not a pharmacist.');
        }

        return $this->prescriptionRepository->updatePrescription($prescription, $attribute);
    }

    public function updatePrescription(Prescription $prescription, array $attributes)
    {
        return $this->prescriptionRepository->updatePrescription($prescription, $attributes);
    }
}
