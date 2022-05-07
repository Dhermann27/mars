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
        Schema::create('churches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('city');
            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->timestamps();
        });
        DB::update('ALTER TABLE churches AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('churches');
    }
};
