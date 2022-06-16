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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE expose_roomselection(myyear_id INT)
            BEGIN
                SET SQL_MODE = '';
                TRUNCATE roomselection_expo;
                INSERT INTO roomselection_expo (room_id, names, created_at)
                    SELECT r.id,
                        IF(c.id IS NULL OR MAX(ya.is_private)=0,GROUP_CONCAT(CONCAT(c.firstname, ' ', c.lastname) ORDER BY c.birthdate SEPARATOR '<br />'),'Private occupant(s)') names,
                        NOW()
                    FROM (rooms r, buildings b)
                        LEFT OUTER JOIN (yearsattending ya, campers c) ON r.id=ya.room_id AND ya.year_id=myyear_id AND ya.camper_id=c.id
                    WHERE r.building_id=b.id AND r.xcoord IS NOT NULL AND r.ycoord IS NOT NULL AND r.pixelsize IS NOT NULL
                    GROUP BY r.id;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS expose_roomselection;');
    }
};
