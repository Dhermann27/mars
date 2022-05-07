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
        Schema::create('contactboxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('emails');
            $table->timestamps();
        });
        DB::update('ALTER TABLE contactboxes AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactboxes');
    }
};
