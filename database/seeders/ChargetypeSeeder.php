<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChargetypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chargetypes')->insert([
            ['name' => 'Housing Fee', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Pre-Reg Check Deposit', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Workshop Fee', 'is_shown' => 1, 'is_deposited' => 0],
            ['name' => 'Deposit', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Housing Deposit', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Paypal Payment', 'is_shown' => 1, 'is_deposited' => 1],
            ['name' => 'Check Payment', 'is_shown' => 1, 'is_deposited' => 1],
            ['name' => 'Credit Card Payment', 'is_shown' => 1, 'is_deposited' => 1],
            ['name' => 'MUUSA Donation', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'Carryover Bill', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Other', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'Deposit Applied', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Late Fee', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'MUUSA Scholarship', 'is_shown' => 1, 'is_deposited' => 0],
            ['name' => 'Early Arrival Fees', 'is_shown' => 1, 'is_deposited' => 0],
            ['name' => 'Pre-Registration for Next Year', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'Pre-Reg Credit Card Deposit', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Cash', 'is_shown' => 1, 'is_deposited' => 1],
            ['name' => 'Refund Issued', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'YMCA Scholarship', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'YMCA Donation', 'is_shown' => 1, 'is_deposited' => 0,],
            ['name' => 'Staff Credit', 'is_shown' => 1, 'is_deposited' => 0],
            ['name' => 'Pre-Reg Paypal Deposit', 'is_shown' => 0, 'is_deposited' => 0],
            ['name' => 'Paypal Service Charge', 'is_shown' => 1, 'is_deposited' => 1]
        ]);
    }
}

