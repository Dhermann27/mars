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
        DB::unprepared("CREATE VIEW byyear_charges AS
                          SELECT
                            h.id,
                            y.id                                 year_id,
                            y.year,
                            c.family_id,
                            h.camper_id,
                            h.amount,
                            h.deposited_date,
                            h.chargetype_id,
                            g.name                              chargetypename,
                            h.timestamp,
                            h.memo,
                            h.parent_id,
                            h.created_at
                          FROM charges h, chargetypes g, campers c, years y
                          WHERE h.chargetype_id=g.id AND h.camper_id=c.id AND h.year_id=y.id
                          UNION ALL
                          SELECT
                            0,
                            yg.id,
                            yg.year,
                            c.family_id,
                            hg.camper_id,
                            hg.charge,
                            NULL,
                            g.id,
                            g.name,
                            NULL,
                            hg.memo,
                            NULL,
                            NULL
                          FROM campers c, gencharges hg, chargetypes g, years yg
                          WHERE c.id=hg.camper_id AND g.id=hg.chargetype_id AND hg.year_id=yg.id
                          UNION ALL
                          SELECT
                            0,
                            oy.id,
                            oy.year,
                            c.family_id,
                            og.camper_id,
                            og.charge,
                            NULL,
                            g.id,
                            g.name,
                            NULL,
                            og.memo,
                            NULL,
                            NULL
                          FROM campers c, oldgencharges og, chargetypes g, years oy
                          WHERE c.id=og.camper_id AND g.id=og.chargetype_id AND og.year_id=oy.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS byyear_charges;');
    }
};
