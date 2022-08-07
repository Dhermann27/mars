<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE `expose_parentschild_family`(myfamily_id INT)
            BEGIN
                SET sql_mode='';
                DELETE FROM parents__child_expo WHERE child_yearattending_id IN (SELECT ya.id FROM yearsattending ya, campers c WHERE ya.camper_id=c.id AND c.family_id=myfamily_id);
                INSERT INTO parents__child_expo (child_yearattending_id, parent_yearattending_id, created_at)
                    SELECT ya.id, yap.id, NOW()
                        FROM
	                        (campers c, yearsattending ya)
	                    LEFT OUTER JOIN (campers cp, yearsattending yap)
	                        ON (cp.id=yap.camper_id AND ya.year_id=yap.year_id AND c.family_id=myfamily_id
	                            AND c.family_id=cp.family_id AND getrealage(cp.birthdate, yap.year_id) >= 18)
                        WHERE c.id=ya.camper_id AND c.family_id=myfamily_id
                            AND getrealage(c.birthdate, ya.year_id)<18
                    UNION
                    SELECT ya.id, yap.id, NOW()
                        FROM
	                        (campers c, yearsattending ya)
	                    RIGHT OUTER JOIN (campers cp, yearsattending yap)
	                        ON (cp.id=yap.camper_id AND ya.year_id=yap.year_id AND c.family_id=myfamily_id
	                            AND c.family_id=cp.family_id AND getrealage(cp.birthdate, yap.year_id) >= 18)
                        WHERE c.id=ya.camper_id AND c.family_id=myfamily_id
                            AND getrealage(c.birthdate, ya.year_id)<18;
                UPDATE parents__child_expo pc, yearsattending ya, campers c, campers cp, yearsattending yap
                    SET pc.parent_yearattending_id=yap.id
                    WHERE pc.parent_yearattending_id IS NULL AND cp.id=yap.camper_id
                        AND pc.child_yearattending_id=ya.id AND ya.camper_id=c.id AND c.family_id=myfamily_id
                        AND c.sponsor LIKE CONCAT(cp.firstname, ' ', cp.lastname);
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS expose_parentschild_family');
    }
};
