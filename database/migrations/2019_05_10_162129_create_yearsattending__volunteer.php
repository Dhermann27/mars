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
        Schema::create('yearsattending__volunteer', function (Blueprint $table) {
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
            $table->unsignedBigInteger('volunteerposition_id');
            $table->foreign('volunteerposition_id')->references('id')->on('volunteerpositions');
            $table->timestamps();
            $table->unique(array('yearattending_id', 'volunteerposition_id'), 'yaid__vpid_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yearsattending__volunteer');
    }
};
