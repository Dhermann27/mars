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
        // Assumes yearsattending with higher id are later
        DB::unprepared("CREATE VIEW campers_view AS
            SELECT c.id, ya.days as currentdays, c.family_id, c.pronoun_id, c.firstname, c.lastname, c.email,
              CONCAT(SUBSTR(c.phonenbr, 1, 3), '-', SUBSTR(c.phonenbr, 4, 3), '-', SUBSTR(c.phonenbr, 7, 4)) AS phone,
              c.birthdate, c.roommate, c.sponsor, c.is_handicap, c.foodoption_id, c.church_id,
              IFNULL(ya.program_id, (SELECT yap.program_id FROM yearsattending yap WHERE yap.camper_id=c.id ORDER BY ya.id DESC LIMIT 1)) AS program_id
             FROM campers c
             LEFT JOIN (yearsattending ya, years y) ON ya.camper_id=c.id AND ya.year_id=y.id AND y.is_current=1
             ORDER BY birthdate");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS campers_view;');
    }
};
