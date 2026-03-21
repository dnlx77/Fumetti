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
        Schema::table('albo', function (Blueprint $table) {
            $table->foreign(['collana_id'])->references(['id'])->on('collana')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['editore_id'])->references(['id'])->on('editore')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albo', function (Blueprint $table) {
            $table->dropForeign('albo_collana_id_foreign');
            $table->dropForeign('albo_editore_id_foreign');
            $table->dropForeign('albo_user_id_foreign');
        });
    }
};
