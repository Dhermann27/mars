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
        Schema::create('programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->integer('order');
            $table->text('blurb')->nullable();
            $table->text('letter')->nullable();
            $table->text('covenant')->nullable();
            $table->text('calendar')->nullable();
            $table->tinyInteger('is_program_housing')->default(0);
            $table->tinyInteger('is_minor')->default(0);
            $table->timestamps();
        });
        DB::update('ALTER TABLE programs AUTO_INCREMENT = 1000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
};
