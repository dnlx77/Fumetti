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
        Schema::table('rel_storia_autore_ruolo', function (Blueprint $table) {
            $table->foreign(['autore_id'])->references(['id'])->on('autore')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ruolo_id'])->references(['id'])->on('ruolo')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['storia_id'])->references(['id'])->on('storia')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rel_storia_autore_ruolo', function (Blueprint $table) {
            $table->dropForeign('rel_storia_autore_ruolo_autore_id_foreign');
            $table->dropForeign('rel_storia_autore_ruolo_ruolo_id_foreign');
            $table->dropForeign('rel_storia_autore_ruolo_storia_id_foreign');
        });
    }
};
