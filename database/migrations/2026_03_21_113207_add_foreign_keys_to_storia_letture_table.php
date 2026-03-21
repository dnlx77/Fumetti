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
        Schema::table('storia_letture', function (Blueprint $table) {
            $table->foreign(['storia_id'])->references(['id'])->on('storia')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storia_letture', function (Blueprint $table) {
            $table->dropForeign('storia_letture_storia_id_foreign');
            $table->dropForeign('storia_letture_user_id_foreign');
        });
    }
};
