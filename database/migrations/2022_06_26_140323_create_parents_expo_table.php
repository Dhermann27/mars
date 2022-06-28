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
        Schema::create('parents__child_expo', function (Blueprint $table) {
            $table->unsignedBigInteger('child_yearattending_id')->index('child');
            $table->foreign('child_yearattending_id')->references('id')->on('yearsattending');
            $table->unsignedBigInteger('parent_yearattending_id')->nullable();
            $table->foreign('parent_yearattending_id')->references('id')->on('yearsattending');
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
        Schema::dropIfExists('parents__child_expo');
    }
};
