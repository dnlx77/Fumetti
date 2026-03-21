<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('albo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_pagine')->nullable();
            $table->decimal('prezzo', 10)->nullable();
            $table->decimal('prezzo_lire', 10, 0)->nullable();
            $table->string('barcode', 511)->nullable();
            $table->string('filename', 511)->nullable();
            $table->string('mime', 511)->nullable();
            $table->string('original_filename', 511)->nullable();
            $table->integer('numero')->nullable();
            $table->string('titolo', 511)->nullable();
            $table->date('data_pubblicazione')->nullable();
            $table->unsignedBigInteger('collana_id')->nullable()->index('albo_collana_id_foreign');
            $table->unsignedBigInteger('editore_id')->index('albo_editore_id_foreign');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable()->index('albo_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albo');
    }
};
