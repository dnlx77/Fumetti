<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelStoriaAutoreRuoloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_storia_autore_ruolo', function (Blueprint $table) {
            $table->bigInteger('storia_id')->unsigned();
            $table->bigInteger('autore_id')->unsigned();
            $table->bigInteger('ruolo_id')->unsigned();
            $table->timestamps();
            $table->primary(['storia_id', 'autore_id', 'ruolo_id']);
        });

        Schema::table('rel_storia_autore_ruolo', function(Blueprint $table) {
            $table->foreign('storia_id')->references('id')->on('storia');
            $table->foreign('autore_id')->references('id')->on('autore');
            $table->foreign('ruolo_id')->references('id')->on('ruolo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_storia_autore_ruolo');
    }
}
