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
        Schema::table('rel_albo_autoricopertina', function (Blueprint $table) {
            $table->foreign(['albo_id'])->references(['id'])->on('albo')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['autore_id'])->references(['id'])->on('autore')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rel_albo_autoricopertina', function (Blueprint $table) {
            $table->dropForeign('rel_albo_autoricopertina_albo_id_foreign');
            $table->dropForeign('rel_albo_autoricopertina_autore_id_foreign');
        });
    }
};
