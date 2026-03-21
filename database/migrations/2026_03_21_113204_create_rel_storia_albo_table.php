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
        Schema::create('rel_storia_albo', function (Blueprint $table) {
            $table->unsignedBigInteger('storia_id');
            $table->unsignedBigInteger('albo_id')->index('rel_storia_albo_albo_id_foreign');
            $table->timestamps();

            $table->primary(['storia_id', 'albo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_storia_albo');
    }
};
