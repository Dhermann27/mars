<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE VIEW thisyear_charges AS
                          SELECT
                            bh.id,
                            bh.year_id,
                            y.year,
                            c.family_id,
                            c.id   camper_id,
                            bh.amount,
                            bh.deposited_date,
                            bh.chargetype_id,
                            g.name chargetypename,
                            bh.timestamp,
                            bh.memo,
                            bh.parent_id,
                            bh.created_at
                          FROM campers c, byyear_charges bh, chargetypes g, years y
                          WHERE c.id = bh.camper_id AND bh.chargetype_id = g.id AND bh.year = y.year AND y.is_current = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS thisyear_charges;');
    }
};
