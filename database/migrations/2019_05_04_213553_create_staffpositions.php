<?php

use App\Enums\Pctype;
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
        Schema::create('staffpositions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('compensationlevel_id');
            $table->foreign('compensationlevel_id')->references('id')->on('compensationlevels');
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->tinyInteger('pctype')->default(Pctype::Member);
            $table->integer('start_year')->default(1901);
            $table->integer('end_year')->default(2100);
            $table->timestamps();
        });
        DB::update('ALTER TABLE staffpositions AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staffpositions');
    }
};
