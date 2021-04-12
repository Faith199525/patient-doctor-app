<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionSeeder::class);
        $this->call(BankListSeeder::class);
        $this->call(SpecialtySeeder::class);
        $this->call(AddToSpecialtySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DiagnosticTestSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
    }
}