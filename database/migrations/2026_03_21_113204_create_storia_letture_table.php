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
        Schema::create('storia_letture', function (Blueprint $table) {
            $table->date('data_lettura');
            $table->unsignedBigInteger('storia_id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable()->index('storia_letture_user_id_foreign');

            $table->primary(['storia_id', 'data_lettura']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storia_letture');
    }
};
