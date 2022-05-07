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
        DB::unprepared("CREATE VIEW byyear_families AS
                      SELECT
                        y.id                                           year_id,
                        y.year,
                        f.id,
                        f.address1,
                        f.address2,
                        f.city,
                        pv.code                                        provincecode,
                        f.zipcd,
                        f.country,
                        f.is_address_current,
                        f.is_ecomm,
                        f.is_scholar,
                        COUNT(ya.id)                                   count,
                        SUM(IF(ya.room_id != 0, 1, 0))                 assigned,
                        GROUP_CONCAT(DISTINCT c.lastname ORDER BY c.birthdate SEPARATOR ' / ') familyname,
                        (SELECT SUM(bh.amount)
                         FROM byyear_charges bh
                         WHERE ya.year_id = bh.year AND f.id = bh.family_id) balance,
                        MIN(ya.created_at)                               created_at
                      FROM families f, campers c, yearsattending ya, provinces pv, years y
                      WHERE f.id=c.family_id AND c.id=ya.camper_id AND f.province_id=pv.id AND ya.year_id=y.id
                      GROUP BY y.id, f.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS byyear_families;');
    }
};
