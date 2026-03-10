<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albo', function (Blueprint $table) {
            $table->id();
            $table->integer('num_pagine')->nullable();
            $table->decimal('prezzo', 10, 2)->nullable();
            $table->decimal('prezzo_lire', 10)->nullable();
            $table->string('barcode', 511)->nullable();
            $table->string('filename', 511)->nullable();
            $table->string('mime', 511)->nullable();
            $table->string('original_filename', 511)->nullable();
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
        Schema::dropIfExists('albo');
    }
}
