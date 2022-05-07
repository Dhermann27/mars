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
        DB::unprepared('CREATE FUNCTION getcurrentyear () RETURNS INT DETERMINISTIC BEGIN
 			RETURN(SELECT year FROM years WHERE is_current=1 LIMIT 1);
 		END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS getcurrentyear');
    }
};
