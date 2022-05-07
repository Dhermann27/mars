<?php

use App\Enums\Chargetypename;
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
        DB::unprepared("CREATE VIEW thisyear_staff AS
                          SELECT
                            y.year,
                            year_id,
                            family_id,
                            camper_id,
                            yearattending_id,
                            firstname,
                            lastname,
                            email,
                            staffpositionname,
                            staffposition_id,
                            program_id,
                            max_compensation,
                            pctype
                          FROM byyear_staff bsp, years y
                          WHERE bsp.year = y.year AND y.is_current = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS thisyear_staff;');
    }
};
