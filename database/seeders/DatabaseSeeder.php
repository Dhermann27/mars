<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([BuildingSeeder::class,
            ChargetypeSeeder::class,
            ChartdaySeeder::class,
            CompensationLevelSeeder::class,
            ProgramSeeder::class,
            PronounSeeder::class,
            ProvinceSeeder::class,
            TimeslotSeeder::class]);
    }
}
