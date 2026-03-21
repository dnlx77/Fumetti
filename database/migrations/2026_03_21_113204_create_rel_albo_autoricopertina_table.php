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
        Schema::create('rel_albo_autoricopertina', function (Blueprint $table) {
            $table->unsignedBigInteger('albo_id');
            $table->unsignedBigInteger('autore_id')->index('rel_albo_autoricopertina_autore_id_foreign');
            $table->timestamps();

            $table->primary(['albo_id', 'autore_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_albo_autoricopertina');
    }
};
