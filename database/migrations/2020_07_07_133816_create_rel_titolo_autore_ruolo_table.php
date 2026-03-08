<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelTitoloAutoreRuoloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_titolo_autore_ruolo', function (Blueprint $table) {
            $table->integer('titolo_id');
            $table->integer('autore_id');
            $table->integer('ruolo_id');
            $table->timestamps();
            $table->primary(['titolo_id', 'autore_id', 'ruolo_id']);
        });

        Schema::table('rel_titolo_autore_ruolo', function(Blueprint $table) {
            $table->foreign('titolo_id')->references('id')->on('titolo');
            $table->foreign('autore_id')->references('id')->on('autore');
            $table->foreign('ruolo_id')->nullable()->references('id')->on('ruolo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_titolo_autore_ruolo');
    }
}
