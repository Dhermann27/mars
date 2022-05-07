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
        DB::unprepared("CREATE VIEW directory_view AS
            SELECT LEFT(familyname, 1) AS first, familyname, address1, address2, city, provincecode, GROUP_CONCAT(year) AS years
            FROM byyear_families WHERE is_address_current=1 GROUP BY id ORDER BY familyname, provincecode, city;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS directory_view;');
    }
};
