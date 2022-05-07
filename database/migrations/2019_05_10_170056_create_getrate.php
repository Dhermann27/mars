<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE FUNCTION getrate(mycamperid INT, myyear YEAR)
                          RETURNS FLOAT DETERMINISTIC
                          BEGIN
                            DECLARE occupants, days, program INT DEFAULT 0;
                            SELECT
                              COUNT(*),
                              MAX(ya.days),
                              MAX(ya.program_id)
                            INTO occupants, days, program
                            FROM (campers c, yearsattending ya, yearsattending yap, campers cp, years y)
                            WHERE c.id=mycamperid AND c.id=ya.camper_id AND ya.year_id=y.id AND y.year=myyear
                                AND ya.room_id=yap.room_id AND ya.year_id=yap.year_id AND yap.camper_id=cp.id;
                              RETURN (SELECT IFNULL(hr.rate * days, 0)
                                      FROM yearsattending ya, rooms r, rates hr, years y
                                      WHERE ya.camper_id=mycamperid AND ya.year_id=y.id AND y.year=myyear
                                            AND r.id=ya.room_id AND r.building_id=hr.building_id AND hr.program_id=program
                                            AND occupants >= hr.min_occupancy AND occupants <= hr.max_occupancy
                                            AND myyear >= hr.start_year AND myyear <= hr.end_year);
                          END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS getrate;');
    }
};
