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
        DB::unprepared("CREATE VIEW thisyear_families AS
                       SELECT
                        year_id,
                        y.year,
                        bf.id,
                        address1,
                        address2,
                        city,
                        provincecode,
                        zipcd,
                        country,
                        familyname,
                        is_address_current,
                        is_ecomm,
                        is_scholar,
                        count,
                        assigned,
                        balance,
                        bf.created_at
                      FROM byyear_families bf, years y
                      WHERE bf.year = y.year AND y.is_current = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS thisyear_families;');
    }
};
