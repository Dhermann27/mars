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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE update_workshops(myyear_id INT)
                          BEGIN
                            DECLARE done INT DEFAULT FALSE;
                            DECLARE myid, mycapacity INT;
                            DECLARE cur CURSOR FOR SELECT id, capacity - (IF(led_by LIKE '% and %',2,1)) FROM workshops WHERE year_id=myyear_id;
                            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=TRUE;
                            SET sql_mode='';

                            UPDATE yearsattending__workshop yw, yearsattending ya
                            SET is_enrolled=0
                            WHERE yw.yearattending_id=ya.id AND ya.year_id=myyear_id;

                            UPDATE workshops w
                            SET w.enrolled=(SELECT COUNT(*)
                                              FROM yearsattending__workshop yw
                                              WHERE w.id=yw.workshop_id)
                            WHERE w.year_id=myyear_id;

                            INSERT INTO yearsattending__workshop (yearattending_id, workshop_id, is_leader, created_at)
                                SELECT ya.id, w.id, 1, NOW()
                                    FROM yearsattending ya, campers c, workshops w
                                    WHERE ya.year_id=myyear_id AND ya.camper_id=c.id AND w.year_id=myyear_id AND
                                        w.led_by LIKE CONCAT('%', c.firstname, ' ', c.lastname, '%')
                            ON DUPLICATE KEY UPDATE is_leader=1;

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
        DB::unprepared('DROP PROCEDURE IF EXISTS update_workshops');
    }
};
