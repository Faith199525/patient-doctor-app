<?php
/**
 *  * Author: Akanbi Lawal
 * Date: 28/05/2020
 * Time: 10:36 AM
 */


namespace App\RepositoryContracts;


use App\Common\Contracts\BaseRepositoryContract;
use App\Models\Enums\GenericStatusConstant;
use App\Models\User;
use Dlabs\PaginateApi\PaginateApiAwarePaginator;

interface UserRepository extends BaseRepositoryContract
{
    /**
     * @param array $attributes
     * @return User
     */
    public function save(array $attributes): User;


    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return PaginateApiAwarePaginator
     */
    public function update(User $user, array $attributes);


    /**
     * @param string $status
     * @param int $limit
     * @param int $offset
     * @return PaginateApiAwarePaginator
     */
    public function getUsers($status = GenericStatusConstant::ACTIVE, $limit = 20, $offset = 0);

    /**
     * @param string $identifier
     * @param string $status
     * @return object|null|User
     */
    public function getUserByIdentifier(string $identifier, $status = GenericStatusConstant::ACTIVE);

    public function sendVerificationEmail($email, $token);

    public function verifyUser($token);


    public function getUserByEmail(string $email, $status = GenericStatusConstant::ACTIVE, $role = true): User;

    public function getUserById($id, $status = GenericStatusConstant::ACTIVE, $role = true): User;

    public function generateUserRefreshToken($email);
}
