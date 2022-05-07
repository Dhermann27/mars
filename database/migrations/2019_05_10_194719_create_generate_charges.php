<?php

use App\Enums\Chargetypename;
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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE generate_charges(myyear_id INT)
                  BEGIN
                    SET SQL_MODE = '';
                    DELETE FROM gencharges WHERE year_id=myyear_id;
                    INSERT INTO gencharges (year_id, camper_id, charge, chargetype_id, memo)
                      SELECT
                        bc.year_id,
                        bc.id,
                        getrate(bc.id, bc.year)," . Chargetypename::Fees . ", bc.buildingname
                      FROM byyear_campers bc
                      WHERE bc.room_id!=0 AND bc.year_id=myyear_id;
                    INSERT INTO gencharges (year_id, camper_id, charge, chargetype_id, memo)
                      SELECT
                        ya.year_id,
                        MAX(c.id),
                        IF(COUNT(c.id) = 1, 200.0, 400.0)," . Chargetypename::Deposit . ", CONCAT(\"Deposit for \", y.year)
                      FROM families f, campers c, yearsattending ya, years y
                      WHERE f.id=c.family_id AND c.id=ya.camper_id AND ya.year_id=y.id AND y.id=myyear_id AND ya.room_id IS NULL
                      GROUP BY f.id;
                    INSERT INTO gencharges (year_id, camper_id, charge, chargetype_id, memo)
                      SELECT
                        bsp.year_id,
                        bsp.camper_id,
                        -(LEAST(SUM(bsp.max_compensation), IFNULL(getrate(bsp.camper_id, bsp.year), 200.0))) amount,"
                        . Chargetypename::Staffcredit . ", IF(COUNT(*) = 1, bsp.staffpositionname, 'Staff Position Credits')
                      FROM byyear_staff bsp
                      WHERE bsp.year_id=myyear_id
                      GROUP BY bsp.year, bsp.camper_id;
                    INSERT INTO gencharges (year_id, camper_id, charge, chargetype_id, memo)
                      SELECT
                        ya.year_id,
                        ya.camper_id,
                        w.fee, " . Chargetypename::Workshopfee . ", w.name
                      FROM workshops w, yearsattending__workshop yw, yearsattending ya
                      WHERE w.fee > 0 AND yw.is_enrolled = 1 AND w.id = yw.workshop_id AND yw.yearattending_id = ya.id
                        AND ya.year_id=myyear_id;
                  END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_charges');
    }
};
