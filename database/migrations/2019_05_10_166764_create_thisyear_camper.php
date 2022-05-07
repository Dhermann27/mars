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
        DB::unprepared("CREATE VIEW thisyear_campers AS
                      SELECT
                        year_id,
                        y.year,
                        family_id,
                        familyname,
                        address1,
                        address2,
                        city,
                        provincecode,
                        zipcd,
                        country,
                        bc.id,
                        pronoun_id,
                        pronounname,
                        firstname,
                        lastname,
                        email,
                        phonenbr,
                        birthdate,
                        birthday,
                        age,
                        program_id,
                        programname,
                        is_program_housing,
                        roommate,
                        sponsor,
                        is_handicap,
                        foodoption_id,
                        church_id,
                        churchname,
                        churchcity,
                        churchprovincecode,
                        yearattending_id,
                        days,
                        room_id,
                        room_number,
                        building_id,
                        buildingname
                      FROM byyear_campers bc, years y
                      WHERE bc.year = y.year AND y.is_current = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS thisyear_campers;');
    }
};
