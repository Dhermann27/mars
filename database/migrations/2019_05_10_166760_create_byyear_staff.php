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
        DB::unprepared("CREATE VIEW byyear_staff AS
                          SELECT
                            y.id         year_id,
                            y.year,
                            c.family_id,
                            c.id         camper_id,
                            ya.id        yearattending_id,
                            c.firstname,
                            c.lastname,
                            c.email,
                            sp.name      staffpositionname,
                            sp.id        staffposition_id,
                            cl.max_compensation +
                            IF(ysp.is_eaf_paid = 1, (SELECT IFNULL(SUM(h.amount), 0)
                                                     FROM charges h
                                                     WHERE h.camper_id IN (SELECT cp.id
                                                                          FROM campers cp
                                                                          WHERE cp.family_id = c.family_id) AND h.year_id = ya.year_id AND
                                                           h.chargetype_id = " . Chargetypename::Earlyarrival . "), 0)
                                         max_compensation,
                            sp.program_id program_id,
                            sp.pctype    pctype,
                            ysp.created_at
                          FROM campers c, yearsattending ya, yearsattending__staff ysp, staffpositions sp, compensationlevels cl, years y
                          WHERE c.id = ya.camper_id AND ya.id = ysp.yearattending_id AND ysp.staffposition_id = sp.id
                                AND sp.compensationlevel_id = cl.id AND ya.year_id=y.id AND y.year >= sp.start_year AND y.year <= sp.end_year
                          UNION ALL
                          SELECT
                            y.id,
                            y.year,
                            c.family_id,
                            c.id camper_id,
                            0,
                            c.firstname,
                            c.lastname,
                            c.email,
                            sp.name,
                            sp.id,
                            cl.max_compensation,
                            sp.program_id,
                            sp.pctype,
                            cs.created_at
                          FROM camper__staff cs, campers c, staffpositions sp, compensationlevels cl, years y
                          WHERE cs.camper_id = c.id AND cs.staffposition_id = sp.id AND y.is_current = 1 AND
                                sp.compensationlevel_id = cl.id AND y.year >= sp.start_year AND y.year <= sp.end_year;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP VIEW IF EXISTS byyear_staff;');
    }
};
