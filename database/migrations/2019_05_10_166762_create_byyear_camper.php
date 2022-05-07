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
        DB::unprepared("CREATE VIEW byyear_campers AS
                    SELECT
                        y.id                                 year_id,
                        y.year,
                        bf.id                                 family_id,
                        bf.familyname,
                        bf.address1,
                        bf.address2,
                        bf.city,
                        bf.provincecode,
                        bf.zipcd,
                        bf.country,
                        bf.is_ecomm,
                        bf.is_address_current,
                        c.id,
                        c.pronoun_id,
                        o.name                               pronounname,
                        c.firstname,
                        c.lastname,
                        c.email,
                        c.phonenbr,
                        c.birthdate,
                        DATE_FORMAT(c.birthdate, '%m/%d/%Y') birthday,
                        getage(c.birthdate, y.year)         age,
                        p.id                                 program_id,
                        p.name                               programname,
                        p.is_program_housing                 is_program_housing,
                        c.roommate,
                        c.sponsor,
                        c.is_handicap,
                        c.foodoption_id,
                        u.id                                 church_id,
                        u.name                               churchname,
                        u.city                               churchcity,
                        pvp.code                            churchprovincecode,
                        ya.id                                yearattending_id,
                        ya.days,
                        ya.room_id,
                        r.room_number,
                        b.id                                 building_id,
                        b.name                               buildingname
                      FROM (byyear_families bf, campers c, yearsattending ya, programs p, pronouns o, years y)
                        LEFT JOIN (buildings b, rooms r) ON ya.room_id = r.id AND r.building_id = b.id
                        LEFT JOIN (churches u, provinces pvp) ON c.church_id=u.id AND u.province_id=pvp.id
                      WHERE bf.id = c.family_id AND bf.year_id=y.id AND c.id = ya.camper_id AND p.id = ya.program_id AND ya.year_id=y.id
                            AND c.pronoun_id = o.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS byyear_campers;');
    }
};
