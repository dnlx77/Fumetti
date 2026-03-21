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
        Schema::table('rel_storia_albo', function (Blueprint $table) {
            $table->foreign(['albo_id'])->references(['id'])->on('albo')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['storia_id'])->references(['id'])->on('storia')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rel_storia_albo', function (Blueprint $table) {
            $table->dropForeign('rel_storia_albo_albo_id_foreign');
            $table->dropForeign('rel_storia_albo_storia_id_foreign');
        });
    }
};
