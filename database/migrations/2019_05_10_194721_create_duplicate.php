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
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE `duplicate`(beforeid INT, afterid INT)
              BEGIN
                DECLARE family, count INT DEFAULT 0;
                IF beforeid != 0 AND afterid != 0
                THEN
                  SELECT family_id
                  INTO family
                  FROM campers
                  WHERE id = beforeid;
                  DELETE ya FROM yearsattending ya
                    LEFT JOIN yearsattending yap ON ya.camper_id=beforeid AND yap.camper_id=afterid AND ya.year_id=yap.year_id
                    WHERE yap.id IS NOT NULL;
                  UPDATE yearsattending ya
                  SET ya.camper_id = afterid
                  WHERE ya.camper_id = beforeid;
                  UPDATE oldgencharges
                  SET camper_id = afterid
                  WHERE camper_id = beforeid;
                  UPDATE gencharges
                  SET camper_id = afterid
                  WHERE camper_id = beforeid;
                  UPDATE charges
                  SET camper_id = afterid
                  WHERE camper_id = beforeid;
                  DELETE FROM campers
                  WHERE id = beforeid;
                  SELECT COUNT(*)
                  INTO count
                  FROM campers
                  WHERE family_id = family;
                  IF count = 0
                  THEN
                    DELETE FROM families
                    WHERE id = family;
                  END IF;
                END IF;
              END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS duplicate');
    }
};
