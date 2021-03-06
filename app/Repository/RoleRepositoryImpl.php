<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 06/04/2020
 * Time: 11:07 PM
 */

namespace App\Repository;


use App\Common\BaseRepository;
use App\Models\Enums\GenericStatusConstant;
use App\Models\Role;
use App\Models\User;
use App\RepositoryContracts\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RoleRepositoryImpl extends BaseRepository implements RoleRepository
{

    public function __construct(Role $role)
    {
        $this->model = $role;
    }


    /**
     * @param $attributes
     * @return Role
     */
    public function save($attributes): Role
    {

        $role = new Role();
        $role->name = $attributes['name'];
        $role->code = null;
        $role->save();
        return $role;
    }


    /**
     * @param $roles
     * @return Collection
     */
    public function findByNameInRoles($roles): Collection
    {
        $roleNames = array_map(function ($role) {
            return $role['name'];
        }, $roles);

        return $this->where('status', GenericStatusConstant::ACTIVE)
            ->where(function ($query) use ($roleNames) {
                $query->whereIn('name', $roleNames);
            })
            ->get();

    }


    /**
     * @param string $status
     * @return int
     */
    public function getRoleCount($status = GenericStatusConstant::ACTIVE): int
    {
        return $this->countByColumns([
            'status' => $status]);
    }


    /**
     * @param string $code
     * @param string $status
     * @return Role | Model
     */
    public function findRoleByCode(string $code, $status = GenericStatusConstant::ACTIVE): Role
    {
        return $this
//            ->where('status', $status)
            ->where(function ($query) use ($code) {
                $query->where('code', $code);
            })->firstOrFail();

    }

    public function assignRoleTo(int $roleId, User $user): bool
    {
        $attached = $user->roles()->syncWithoutDetaching([$roleId]);
        if ($attached)
            return true;
        return false;
    }
}
