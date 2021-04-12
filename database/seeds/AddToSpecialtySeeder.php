<?php

use App\Models\Enums\GenericStatusConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddToSpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addedSpecialties = [
            ['name' => 'HIV/Tuberculosis', 'code' => 'SPELTY017', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sickle Cell', 'code' => 'SPELTY018', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psycologist', 'code' => 'SPELTY019', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hypertensive & Diabetes', 'code' => 'SPELTY020', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('specialties')->insert($addedSpecialties);
    }
}
