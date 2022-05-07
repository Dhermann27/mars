<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings');
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->integer('min_occupancy');
            $table->integer('max_occupancy');
            $table->double('rate');
            $table->integer('start_year')->default(1901);
            $table->integer('end_year')->default(2100);
            $table->timestamps();
        });
        DB::update('ALTER TABLE rates AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
};
