<?php

use App\Models\Enums\GenericStatusConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BankListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            ['name' => 'Access Bank', 'code' => 'BK001', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Zenith Bank', 'code' => 'BK002', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fidelity Bank', 'code' => 'BK003', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'First Bank', 'code' => 'BK004', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GT Bank', 'code' => 'BK005', 'status' => GenericStatusConstant::ACTIVE, 'created_at' => now(), 'updated_at' => now()],
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('bank_lists')->truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('bank_lists')->insert($banks);
    }
}
