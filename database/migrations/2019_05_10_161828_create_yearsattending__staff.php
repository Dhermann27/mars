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
        Schema::create('yearsattending__staff', function (Blueprint $table) {
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
            $table->unsignedBigInteger('staffposition_id');
            $table->foreign('staffposition_id')->references('id')->on('staffpositions');
            $table->tinyInteger('is_eaf_paid');
            $table->timestamps();
            $table->unique(array('yearattending_id', 'staffposition_id'), 'yaid__spid_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yearsattending__staff');
    }
};
