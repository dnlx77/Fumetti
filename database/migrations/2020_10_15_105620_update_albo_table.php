<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAlboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('albo', function (Blueprint $table) {
            $table->integer('numero')->nullable();
            $table->string('titolo', 511)->nullable();
            $table->date('data_pubblicazione')->nullable();
            $table->bigInteger('collana_id')->unsigned()->nullable();
            $table->bigInteger('editore_id')->unsigned();
        });

        Schema::table('albo', function (Blueprint $table) {
            $table->foreign('collana_id')->references('id')->on('collana');
            $table->foreign('editore_id')->references('id')->on('editore');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
