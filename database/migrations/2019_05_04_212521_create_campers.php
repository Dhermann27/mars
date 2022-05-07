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
        Schema::create('campers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('family_id');
            $table->foreign('family_id')->references('id')->on('families');
            $table->unsignedBigInteger('pronoun_id')->nullable();
            $table->foreign('pronoun_id')->references('id')->on('pronouns');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phonenbr')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('roommate')->nullable();
            $table->string('sponsor')->nullable();
            $table->tinyInteger('is_handicap')->default(0);
            $table->unsignedBigInteger('foodoption_id');
            $table->foreign('foodoption_id')->references('id')->on('foodoptions');
            $table->unsignedBigInteger('church_id')->nullable();
            $table->foreign('church_id')->references('id')->on('churches');
            $table->timestamps();
        });
        DB::update('ALTER TABLE campers AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campers');
    }
};
