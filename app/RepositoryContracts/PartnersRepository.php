<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 28/05/2020
 * Time: 10:36 AM
 */


namespace App\RepositoryContracts;


use App\Common\Contracts\BaseRepositoryContract;
use App\Models\Enums\GenericStatusConstant;
use App\Models\Partners;
use Dlabs\PaginateApi\PaginateApiAwarePaginator;

interface PartnersRepository extends BaseRepositoryContract
{
    /**
     * @param array $attributes
     * @return Partners
     */
    public function save(array $attributes): Partners;


    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return PaginateApiAwarePaginator
     */
    public function getAllPartners($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0);

    public function getPartner($id, $status = GenericStatusConstant::ACTIVE): Partners;

    public function getMembers(Partners $partners);

    public function addMembers(Partners $partners, $user);

    public function addBranches(Partners $partners, array $attributes);
}
