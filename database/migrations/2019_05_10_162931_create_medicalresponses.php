<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('medicalresponses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('yearattending_id');
            $table->foreign('yearattending_id')->references('id')->on('yearsattending');
            $table->string('parent_name');
            $table->string('youth_sponsor');
            $table->string('mobile_phone');
            $table->text('concerns');
            $table->string('doctor_name');
            $table->string('doctor_nbr');
            $table->tinyInteger('is_insured');
            $table->string('holder_name');
            $table->date('holder_birthday');
            $table->string('carrier');
            $table->string('carrier_nbr');
            $table->string('carrier_id');
            $table->string('carrier_group');
            $table->tinyInteger('is_epilepsy');
            $table->tinyInteger('is_diabetes');
            $table->tinyInteger('is_add');
            $table->tinyInteger('is_adhd');
            $table->timestamps();
        });
        DB::update('ALTER TABLE medicalresponses AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicalresponses');
    }
};
