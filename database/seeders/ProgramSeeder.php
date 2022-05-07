<?php

namespace Database\Seeders;

use App\Enums\Programname;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('programs')->insert([
            ['id' => Programname::Meyer, 'name' => 'Meyer', 'title' => 'Junior High', 'blurb' => __('programblurbs.' . Programname::Meyer), 'order' => 5, 'is_program_housing' => 1, 'is_minor' => 1],
            ['id' => Programname::Cratty, 'name' => 'Cratty', 'title' => 'Elementary', 'blurb' => __('programblurbs.' . Programname::Cratty), 'order' => 6, 'is_program_housing' => 0, 'is_minor' => 1],
            ['id' => Programname::Burt, 'name' => 'Burt', 'title' => 'Senior High', 'blurb' => __('programblurbs.' . Programname::Burt), 'order' => 4, 'is_program_housing' => 1, 'is_minor' => 1],
            ['id' => Programname::YoungAdultUnderAge, 'name' => 'YA 18-20', 'title' => 'Underage YAs', 'blurb' => null, 'order' => 3, 'is_program_housing' => 0, 'is_minor' => 0],
            ['id' => Programname::Lumens, 'name' => 'Lumens', 'title' => 'Nursery', 'blurb' => __('programblurbs.' . Programname::Lumens), 'order' => 7, 'is_program_housing' => 0, 'is_minor' => 1],
            ['id' => Programname::Adult, 'name' => 'Adult', 'title' => 'Adult', 'blurb' => __('programblurbs.' . Programname::Adult), 'order' => 1, 'is_program_housing' => 0, 'is_minor' => 0],
            ['id' => Programname::YoungAdult, 'name' => 'YA', 'title' => 'YAs', 'blurb' => __('programblurbs.' . Programname::YoungAdult), 'order' => 2, 'is_program_housing' => 0, 'is_minor' => 0]
        ]);
    }
}
