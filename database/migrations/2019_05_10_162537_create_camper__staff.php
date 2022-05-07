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
        Schema::create('camper__staff', function (Blueprint $table) {
            $table->unsignedBigInteger('camper_id');
            $table->foreign('camper_id')->references('id')->on('campers');
            $table->unsignedBigInteger('staffposition_id');
            $table->foreign('staffposition_id')->references('id')->on('staffpositions');
            $table->timestamps();
            $table->unique(array('camper_id', 'staffposition_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camper__staff');
    }
};
