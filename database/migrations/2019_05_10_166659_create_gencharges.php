<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gencharges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('camper_id');
            $table->foreign('camper_id')->references('id')->on('campers');
            $table->float('charge');
            $table->string('memo')->nullable();
            $table->unsignedBigInteger('chargetype_id');
            $table->foreign('chargetype_id')->references('id')->on('chargetypes');
            $table->unsignedBigInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years');
        });
        DB::update('ALTER TABLE gencharges AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gencharges');
    }
};
