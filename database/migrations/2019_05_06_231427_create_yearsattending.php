<?php

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
        Schema::create('yearsattending', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('camper_id');
            $table->foreign('camper_id')->references('id')->on('campers');
            $table->unsignedBigInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->integer('days')->default('6');
            $table->tinyInteger('is_setbyadmin')->default('0');
            $table->tinyInteger('is_private')->default('0');
            $table->string('nametag')->default('222215521');
            $table->timestamps();
            $table->index(['camper_id', 'year_id']);
        });
        DB::update('ALTER TABLE yearsattending AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yearsattending');
    }
};
