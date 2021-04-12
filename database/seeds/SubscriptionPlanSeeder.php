<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscription = [
            ['type' => 'Standard', 'plan' => 'Monthly', 'amount' => '1000', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Standard', 'plan' => 'quarterly', 'amount' => '2800', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Standard', 'plan' => 'semi_annual', 'amount' => '5500', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Standard', 'plan' => 'annual', 'amount' => '11000', 'created_at' => now(), 'updated_at' => now()],

            ['type' => 'Premium', 'plan' => 'Monthly', 'amount' => '2500', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Premium', 'plan' => 'quarterly', 'amount' => '7200', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Premium', 'plan' => 'semi_annual', 'amount' => '14000', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'Premium', 'plan' => 'annual', 'amount' => '27000', 'created_at' => now(), 'updated_at' => now()],
            
        ];

        DB::table('subscription_plans')->insert($subscription);
    }
}
