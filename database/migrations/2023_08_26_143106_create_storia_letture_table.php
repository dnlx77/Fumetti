<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriaLettureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storia_letture', function (Blueprint $table) {
            $table->date('data_lettura');
            $table->bigInteger('storia_id')->unsigned();
            $table->timestamps();
            $table->primary(['storia_id', 'data_lettura']);
        });

        Schema::table('storia_letture', function (Blueprint $table) {
            $table->foreign('storia_id')->references('id')->on('storia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_storia_letture');
    }
}