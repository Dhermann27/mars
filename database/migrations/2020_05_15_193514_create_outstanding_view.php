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
        DB::unprepared("CREATE VIEW outstanding_view AS
            SELECT bh.family_id, (SELECT GROUP_CONCAT(DISTINCT c.lastname ORDER BY c.birthdate SEPARATOR ' / ') FROM campers c WHERE bh.family_id=c.family_id) familyname,
            SUM(IF(bh.year=getcurrentyear()-1,bh.amount,0)) lastamount, SUM(IF(bh.year=getcurrentyear(),bh.amount,0)) thisamount
            FROM byyear_charges bh WHERE bh.year>=getcurrentyear()-1 GROUP BY bh.family_id
            HAVING lastamount!=0 OR thisamount!=0 ORDER BY familyname");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS outstanding_view;');
    }
};
