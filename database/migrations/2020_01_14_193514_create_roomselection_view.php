<?php

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
        DB::unprepared("CREATE VIEW roomselection_view AS
            SELECT r.id, r.building_id, b.name buildingname, r.room_number, r.capacity, r.xcoord,
            r.ycoord, r.pixelsize, rp.room_number connected_with,
            GROUP_CONCAT(CONCAT(c.firstname, ' ', c.lastname) ORDER BY c.birthdate SEPARATOR '<br />') names,
            IF(c.id IS NULL OR r.capacity>=10,1,0) available FROM (rooms r, buildings b)
            LEFT OUTER JOIN (yearsattending ya, campers c, years y) ON r.id=ya.room_id AND ya.year_id=y.id AND y.is_current=1 AND ya.camper_id=c.id
            LEFT OUTER JOIN rooms rp ON r.connected_with=rp.id WHERE r.building_id=b.id AND r.xcoord>0 AND r.ycoord>0
            GROUP BY id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS roomselection_view;');
    }
};
