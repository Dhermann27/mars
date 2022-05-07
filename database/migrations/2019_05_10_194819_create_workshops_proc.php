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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE workshops()
                          BEGIN
                            DECLARE done INT DEFAULT FALSE;
                            DECLARE myid, mycapacity INT;
                            DECLARE cur CURSOR FOR SELECT
                                                     id,
                                                     capacity - 1
                                                   FROM workshops;
                            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=TRUE;
                            SET sql_mode='';

                            UPDATE yearsattending__workshop
                            SET is_enrolled=0;

                            UPDATE workshops w
                            SET w.enrolled=(SELECT COUNT(*)
                                              FROM yearsattending__workshop yw
                                              WHERE w.id=yw.workshop_id);
                            UPDATE yearsattending__workshop yw, thisyear_campers tc, workshops w
                            SET yw.is_leader=1
                            WHERE yw.workshop_id=w.id AND yw.yearattending_id=tc.yearattending_id
                                  AND w.led_by LIKE CONCAT('%', tc.firstname, ' ', tc.lastname, '%');

                            OPEN cur;

                            read_loop: LOOP
                              FETCH cur
                              INTO myid, mycapacity;
                              IF done
                              THEN
                                LEAVE read_loop;
                              END IF;
                              UPDATE yearsattending__workshop yw
                              SET yw.is_enrolled=1
                              WHERE yw.workshop_id=myid AND (yw.is_leader=1 OR
                                                              yw.created_at <= (SELECT MAX(created_at)
                                                                                FROM
                                                                                  (SELECT ywp.created_at
                                                                                   FROM yearsattending__workshop ywp
                                                                                   WHERE ywp.workshop_id=myid AND ywp.is_leader=0
                                                                                   ORDER BY created_at
                                                                                   LIMIT mycapacity)
                                                                                    AS t1));
                            END LOOP;

                            CLOSE cur;

                          END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS workshops');
    }
};
