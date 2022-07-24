<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        DB::unprepared("CREATE FUNCTION getnametag(yaid INT, line INT)
          RETURNS VARCHAR(255) DETERMINISTIC
          BEGIN
            RETURN (SELECT CASE
                WHEN SUBSTR(ya.nametag,3+line,1) = 1 THEN (SELECT IFNULL(ch.name,\"\") FROM churches ch, campers c WHERE ya.camper_id=c.id AND c.church_id=ch.id)
                WHEN SUBSTR(ya.nametag,3+line,1) = 2 THEN (SELECT CONCAT(f.city,', ',p.name) FROM families f, campers c, provinces p WHERE ya.camper_id=c.id AND c.family_id=f.id AND f.province_id=p.id)
                WHEN SUBSTR(ya.nametag,3+line,1) = 3 THEN (SELECT IFNULL(sp.name,\"\") FROM staffpositions sp, yearsattending__staff ys, compensationlevels cl  WHERE ya.id=ys.yearattending_id AND ys.staffposition_id=sp.id AND sp.compensationlevel_id=cl.id ORDER BY cl.max_compensation DESC LIMIT 1)
                WHEN SUBSTR(ya.nametag,3+line,1) = 4 THEN \"First-time Camper\"
                ELSE \"\"
            END
            FROM yearsattending ya WHERE ya.id=yaid);
          END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS getnametag;');
    }
};
