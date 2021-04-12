<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'view doctor profile' , 'code' => 'PERM001']);
        Permission::create(['name' => 'view patient profile' , 'code' => 'PERM002']);
        Permission::create(['name' => 'view patient case' , 'code' => 'PERM003']);
        Permission::create(['name' => 'view doctors' , 'code' => 'PERM004']);
        Permission::create(['name' => 'view patients' , 'code' => 'PERM005']);

        // create roles and assign created permissions

        // this can be done as separate statements
        Role::create(['name' => 'super-admin', 'code' => 'ROLE001']);
        Role::create(['name' => 'admin', 'code' => 'ROLE002']);
        Role::create(['name' => 'patient', 'code' => 'ROLE003']);
        Role::create(['name' => 'doctor', 'code' => 'ROLE004']);
        Role::create(['name' => 'diagnostic', 'code' => 'ROLE005']);
        Role::create(['name' => 'ambulance', 'code' => 'ROLE006']);
        Role::create(['name' => 'hospital', 'code' => 'ROLE007']);
        Role::create(['name' => 'pharmacy', 'code' => 'ROLE008']);
        Role::create(['name' => 'nurse', 'code' => 'ROLE009']);
        Role::create(['name' => 'nutritionist', 'code' => 'ROLE010']);

    }
}
