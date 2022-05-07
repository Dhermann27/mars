<?php

use App\Enums\Provincecode;
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
        Schema::create('families', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('address1')->default('NEED ADDRESS');
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('province_id')->default(Provincecode::MO);
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->string('zipcd')->nullable();
            $table->string('country')->nullable();
            $table->tinyInteger('is_address_current')->default('1');
            $table->tinyInteger('is_ecomm')->default('1');
            $table->tinyInteger('is_scholar')->default('0');
            $table->timestamps();
        });
        DB::update('ALTER TABLE families AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('families');
    }
};
