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
        Schema::create('chartdata_days', function (Blueprint $table) {
            $table->integer('year');
            $table->string('onlyday');
            $table->date('dayofyear');
            $table->integer('count');
        });
        Schema::create('chartdata_newcampers', function (Blueprint $table) {
            $table->year('year');
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
        });
        Schema::create('chartdata_oldcampers', function (Blueprint $table) {
            $table->year('year');
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
        });
        Schema::create('chartdata_veryoldcampers', function (Blueprint $table) {
            $table->year('year');
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
        });
        Schema::create('chartdata_lostcampers', function (Blueprint $table) {
            $table->unsignedBigInteger('camper_id');
            $table->foreign('camper_id')->references('id')->on('campers');
            $table->year('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartdata_days');
        Schema::dropIfExists('chartdata_newcampers');
        Schema::dropIfExists('chartdata_oldcampers');
        Schema::dropIfExists('chartdata_veryoldcampers');
        Schema::dropIfExists('chartdata_lostcampers');
    }
};
