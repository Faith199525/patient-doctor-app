<?php


namespace App\Repository;


use App\Common\BaseRepository;
use App\Models\Prescription;
use App\RepositoryContracts\PrescriptionRepository;

class PrescriptionRepositoryImpl extends BaseRepository implements PrescriptionRepository
{

    public function createPrescription(array $attributes)
    {
        //return Prescription::create($attributes);
        return Prescription::updateOrCreate(
            ['case_file_id' => $attributes['case_file_id'] ],
            ['ailment' => $attributes['ailment'], 'diagnosis' =>$attributes['diagnosis'] ]
        );
    }

    public function updatePrescription(Prescription $prescription, array $attributes)
    {
        $prescription->update($attributes);

        return $prescription;
    }

    public function addDrugToPrescription(Prescription $prescription, array $attributes)
    {
        return $prescription->drugs()->create($attributes);
    }

    public function getPrescription($id): Prescription
    {
        return Prescription::where('id', $id)->firstOrFail();
    }
}
