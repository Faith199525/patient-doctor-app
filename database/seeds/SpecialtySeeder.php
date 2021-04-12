<?php

use App\Models\Enums\GenericStatusConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = [
            ['name' => 'General Practitioner', 'code' => 'SPELTY001', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paediatrician', 'code' => 'SPELTY002', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Obstetrician/Gynaecologist', 'code' => 'SPELTY003', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Optician', 'code' => 'SPELTY004', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ophthalmologist', 'code' => 'SPELTY005', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ENT', 'code' => 'SPELTY006', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cardiologist', 'code' => 'SPELTY007', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internal Medicine Physician', 'code' => 'SPELTY008', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orthopaedic Surgeon', 'code' => 'SPELTY009', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Public Health Physician', 'code' => 'SPELTY010', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'General Surgeon', 'code' => 'SPELTY011', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Urologist', 'code' => 'SPELTY012', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gastroenterologist', 'code' => 'SPELTY013', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dermatologist', 'code' => 'SPELTY014', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dentist', 'code' => 'SPELTY015', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oral Surgeon', 'code' => 'SPELTY016', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('specialties')->truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('specialties')->insert($specialties);
    }
}
