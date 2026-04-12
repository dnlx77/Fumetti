<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlboLettureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albo_letture', function (Blueprint $table) {
            $table->date('data_lettura');
            $table->bigInteger('albo_id')->unsigned();
            $table->timestamps();
            $table->primary(['albo_id', 'data_lettura']);
        });

        Schema::table('albo_letture', function (Blueprint $table) {
            $table->foreign('albo_id')->references('id')->on('albo')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albo_letture');
    }
}
