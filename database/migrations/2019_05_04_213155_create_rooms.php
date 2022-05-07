<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->nullable();
            $table->foreign('building_id')->references('id')->on('buildings');
            $table->string('room_number');
            $table->integer('capacity');
            $table->tinyInteger('is_workshop')->default(0);
            $table->tinyInteger('is_handicap')->default(0);
            $table->integer('xcoord');
            $table->integer('ycoord');
            $table->integer('pixelsize');
            $table->integer('connected_with')->default(0);
            $table->timestamps();
        });
        DB::update('ALTER TABLE rooms AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
