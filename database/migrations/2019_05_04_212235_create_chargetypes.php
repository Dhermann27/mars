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
        Schema::create('chargetypes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('is_shown')->default('0');
            $table->tinyInteger('is_deposited')->default('0');
        });
        DB::update('ALTER TABLE chargetypes AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chargetypes');
    }
};
