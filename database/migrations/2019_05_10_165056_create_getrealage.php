<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** TODO: fix to use ID
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE FUNCTION getrealage(birthdate DATE, myyear_id INT)
                          RETURNS INT DETERMINISTIC
                          BEGIN
                            RETURN DATE_FORMAT(FROM_DAYS(DATEDIFF((SELECT checkin
                                                                   FROM years y
                                                                   WHERE y.id=myyear_id), birthdate)), '%Y');
                          END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS getrealage;');
    }
};
