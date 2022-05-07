<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompensationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = array(
            'Level 1' => ['150', '1901', '2100'],
            'Level 2' => ['300', '1901', '2100'],
            'Level 3' => ['450', '1901', '2100'],
            'Level 4' => ['750', '1901', '2100'],
            'Level 5' => ['9999', '1901', '2100']
        );
        foreach ($levels as $name => $info) {
            DB::table('compensationlevels')->insert([
                'name' => $name,
                'max_compensation' => $info[0],
                'start_year' => $info[1],
                'end_year' => $info[2]
            ]);
        }
    }
}
