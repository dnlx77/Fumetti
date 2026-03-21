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
        Schema::table('albo_letture', function (Blueprint $table) {
            $table->foreign(['albo_id'])->references(['id'])->on('albo')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albo_letture', function (Blueprint $table) {
            $table->dropForeign('albo_letture_albo_id_foreign');
            $table->dropForeign('albo_letture_user_id_foreign');
        });
    }
};
