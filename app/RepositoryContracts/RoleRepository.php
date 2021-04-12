<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 28/05/2020
 * Time: 10:12 AM
 */

namespace App\RepositoryContracts;


use App\Common\Contracts\BaseRepositoryContract;
use App\Models\Enums\GenericStatusConstant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepository extends BaseRepositoryContract
{


    /**
     * @param $attributes
     * @return Role
     */
    public function save($attributes): Role;


    /**
     * @param $roles
     * @return Collection
     */
    public function findByNameInRoles($roles): Collection;


    /**
     * @param $status
     * @return int
     */
    public function getRoleCount($status): int;


    /**
     * @param string $code
     * @param string $status
     * @return Role
     */
    public function findRoleByCode(string $code, $status = GenericStatusConstant::ACTIVE): Role;


    public function assignRoleTo(int $roleId, User $user): bool;
}
