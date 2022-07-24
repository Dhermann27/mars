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
        Schema::create('nametag_expo', function (Blueprint $table) {
            $table->unsignedBigInteger('yearattending_id')->index('ya');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
            $table->string('pronoun');
            $table->string('name');
            $table->string('surname');
            $table->string('line1');
            $table->string('line2');
            $table->string('line3');
            $table->string('line4');
            $table->string('font');
            $table->string('icon');
            $table->string('parent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nametag_expo');
    }
};
