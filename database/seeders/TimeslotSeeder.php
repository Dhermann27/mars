<?php

namespace Database\Seeders;

use App\Enums\Timeslotname;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeslotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timeslots = array(
            Timeslotname::Sunrise => ['Sunrise', 'SR', '2001-01-01 06:30:00', '2001-01-01 07:30:00'],
            Timeslotname::Morning => ['Morning', 'M', '2001-01-01 09:50:00', '2001-01-01 11:50:00'],
            Timeslotname::Early_Afternoon => ['Early Afternoon', 'EA', '2001-01-01 13:30:00', '2001-01-01 15:30:00'],
            Timeslotname::Late_Afternoon => ['Late Afternoon', 'LA', '2001-01-01 16:00:00', '2001-01-01 17:30:00'],
            Timeslotname::Evening => ['Evening', 'SS', '2001-01-01 19:30:00', '2001-01-01 20:30:00'],
            Timeslotname::Excursions => ['Excursions', 'EX', '2001-01-01 00:00:00', '2001-01-01 06:00:00']
        );
        foreach ($timeslots as $id => $info) {
            DB::table('timeslots')->insert([
                'id' => $id,
                'name' => $info[0],
                'code' => $info[1],
                'start_time' => $info[2],
                'end_time' => $info[3]
            ]);
        }
    }
}
