<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelStoriaAlbo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_storia_albo', function (Blueprint $table) {
            $table->bigInteger('storia_id')->unsigned();
            $table->bigInteger('albo_id')->unsigned();
            $table->timestamps();
            $table->primary(['storia_id', 'albo_id']);
        });

        Schema::table('rel_storia_albo', function(Blueprint $table) {
            $table->foreign('storia_id')->references('id')->on('storia');
            $table->foreign('albo_id')->references('id')->on('albo')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('rel_storia_albo', function (Blueprint $table) {
            $table->foreign('storia_id')->references('id')->on('storia');
            $table->foreign('albo_id')->references('id')->on('albo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_storia_albo');
    }
}
