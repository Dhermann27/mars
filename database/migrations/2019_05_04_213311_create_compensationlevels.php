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
        Schema::create('compensationlevels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('max_compensation');
            $table->integer('start_year')->default(1901);
            $table->integer('end_year')->default(2100);
            $table->timestamps();
        });
        DB::update('ALTER TABLE compensationlevels AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compensationlevels');
    }
};
