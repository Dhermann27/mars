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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE chartdata()
                BEGIN

                TRUNCATE chartdata_days;
                INSERT INTO chartdata_days (year, onlyday, dayofyear, count)
                    SELECT y.year, RIGHT(sd.dayofyear, 5), ADDDATE(sd.dayofyear, INTERVAL y.year-1 YEAR),
                        COUNT(IF(DATE(ya.created_at)<ADDDATE(sd.dayofyear, INTERVAL y.year-1 YEAR), 1, NULL))
                    FROM chartdays sd, years y, yearsattending ya
                    WHERE y.year>getcurrentyear()-7 AND y.year<=getcurrentyear() AND y.id=ya.year_id AND
                        ADDDATE(sd.dayofyear, INTERVAL y.year-1 YEAR)<NOW()
                    GROUP BY y.id, sd.id ORDER BY sd.id;
                DELETE FROM chartdata_days WHERE count=0;

                TRUNCATE chartdata_newcampers;
                INSERT INTO chartdata_newcampers (year, yearattending_id)
                    SELECT y.year, ya.id
                    FROM years y, yearsattending ya, campers c
                    WHERE y.year>getcurrentyear()-7 AND y.year<=getcurrentyear() AND y.id=ya.year_id AND c.id=ya.camper_id
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id AND yp.year<y.year
                                AND yap.camper_id=c.id)=0;

                TRUNCATE chartdata_oldcampers;
                INSERT INTO chartdata_oldcampers (year, yearattending_id)
                    SELECT y.year, ya.id
                    FROM years y, yearsattending ya, campers c
                    WHERE y.year>getcurrentyear()-7 AND y.year<=getcurrentyear() AND y.id=ya.year_id AND c.id=ya.camper_id
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id AND yp.year=y.year-1
                                AND yap.camper_id=c.id)=0
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id AND yp.year<y.year-1
                                AND yap.camper_id=c.id)>0;

                TRUNCATE chartdata_veryoldcampers;
                INSERT INTO chartdata_veryoldcampers (year, yearattending_id)
                    SELECT y.year, ya.id
                    FROM years y, yearsattending ya, campers c
                    WHERE y.year>getcurrentyear()-7 AND y.year<=getcurrentyear() AND y.id=ya.year_id AND c.id=ya.camper_id
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id
                                AND (yp.year=y.year-1 OR yp.year=y.year-2) AND yap.camper_id=c.id)=0
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id AND yp.year<y.year-2
                                AND yap.camper_id=c.id)>0;

                TRUNCATE chartdata_lostcampers;
                INSERT INTO chartdata_lostcampers (camper_id, year)
                    SELECT c.id, y.year
                    FROM years y, campers c
                    WHERE y.year>getcurrentyear()-7 AND y.year<=getcurrentyear()
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id
                                AND yap.year_id=y.id AND yap.camper_id=c.id)=0
                        AND (SELECT COUNT(*) FROM yearsattending yap, years yp WHERE yap.year_id=yp.id AND yp.year=y.year-1
                                AND yap.camper_id=c.id)>0;

                END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS chartdata');
    }
};
