<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelAlboAutoricopertina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_albo_autoricopertina', function (Blueprint $table) {
            $table->bigInteger('albo_id')->unsigned();
            $table->bigInteger('autore_id')->unsigned();
            $table->timestamps();
            $table->primary(['albo_id', 'autore_id']);
        });

        Schema::table('rel_albo_autoricopertina', function (Blueprint $table) {
            $table->foreign('albo_id')->references('id')->on('albo')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('autore_id')->references('id')->on('autore');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_albo_autoricopertina');
    }
}
