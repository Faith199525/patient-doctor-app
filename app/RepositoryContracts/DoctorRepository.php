<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 28/05/2020
 * Time: 10:36 AM
 */


namespace App\RepositoryContracts;


use App\Common\Contracts\BaseRepositoryContract;
use App\Models\DoctorProfile;
use App\Models\Enums\GenericStatusConstant;
use App\Models\User;
use Dlabs\PaginateApi\PaginateApiAwarePaginator;

interface DoctorRepository extends BaseRepositoryContract
{
    /**
     * @param array $attributes
     * @return User
     */
    public function save(array $attributes): DoctorProfile;


    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return PaginateApiAwarePaginator
     */
    public function getDoctors($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0);


    /**
     * @param string $identifier
     * @param string $status
     * @return object|null|User
     */
    public function getDoctorByIdentifier(string $identifier, $status = GenericStatusConstant::ACTIVE);


    public function getDoctorByEmail(string $email, $status = GenericStatusConstant::ACTIVE, $role = true): User;

    public function storeDoctorCertificate($doctorProfile, $file, $description = null);

    //public function addBranches(DoctorProfile $doctorProfile, array $attributes);
}
