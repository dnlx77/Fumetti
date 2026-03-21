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
        Schema::create('rel_storia_autore_ruolo', function (Blueprint $table) {
            $table->unsignedBigInteger('storia_id');
            $table->unsignedBigInteger('autore_id')->index('rel_storia_autore_ruolo_autore_id_foreign');
            $table->unsignedBigInteger('ruolo_id')->index('rel_storia_autore_ruolo_ruolo_id_foreign');
            $table->timestamps();

            $table->primary(['storia_id', 'autore_id', 'ruolo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_storia_autore_ruolo');
    }
};
