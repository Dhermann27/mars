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
        Schema::create('roomselection_expo', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->unique();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->string('names')->nullable();
            $table->timestamps();
        });
        DB::update('ALTER TABLE roomselection_expo AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roomselection_expo');
    }
};
