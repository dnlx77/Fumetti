<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegui le migrazioni (aggiungiamo user_id alle 3 tabelle).
     */
    public function up(): void
    {
        // 1. Tabella albo
        Schema::table('albo', function (Blueprint $table) {
            // nullable() serve perché hai già dei dati dentro. Se non lo mettessimo,
            // il database andrebbe in errore non sapendo a chi assegnare i vecchi albi.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        // 2. Tabella albo_letture
        Schema::table('albo_letture', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        // 3. Tabella storia_letture
        Schema::table('storia_letture', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Annulla le migrazioni (rimuoviamo user_id se facciamo un rollback).
     */
    public function down(): void
    {
        Schema::table('albo', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('albo_letture', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('storia_letture', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
