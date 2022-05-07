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
        Schema::create('yearsattending__workshop', function (Blueprint $table) {
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
            $table->unsignedBigInteger('workshop_id');
            $table->foreign('workshop_id')->references('id')->on('workshops');
            $table->tinyInteger('is_leader')->default('0');
            $table->tinyInteger('is_enrolled')->default('0');
            $table->timestamps();
            $table->unique(array('yearattending_id', 'workshop_id'), 'yaid__wid_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yearsattending__workshop');
    }
};
