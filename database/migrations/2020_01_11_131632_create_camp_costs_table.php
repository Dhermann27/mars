<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE VIEW camp_costs AS
                          SELECT r.building_id, r.rate
                          FROM rates r, years y, programs p
                          WHERE r.program_id=p.id AND r.start_year<=y.year AND r.end_year>y.year AND y.is_current=1
                          ORDER BY p.name, r.min_occupancy, r.max_occupancy");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS camp_costs;');
    }
};
