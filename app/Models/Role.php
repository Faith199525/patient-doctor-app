<?php

namespace App\Models;

use App\Common\BaseModel;


/**
 * @property null code
 * @property string name
 * @property int id
 */
class Role extends BaseModel
{
    const SUPERADMIN = 'ROLE001';
    const ADMIN = 'ROLE002';
    const PATIENT = 'ROLE003';
    const DOCTOR = 'ROLE004';
    const DIAGNOSTIC = 'ROLE005';
    const AMBULANCE = 'ROLE006';
    const HOSPITAL = 'ROLE007';
    const PHARMACY = 'ROLE008';
    const NURSE = 'ROLE009';
    const NUTRITIONIST = 'ROLE010';

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    public function permissions()
    {
        return $this
            ->belongsToMany(Permission::class, 'role_permissions')
            ->using(RolePermission::class)
            ->as('role_permissions');
    }

}
