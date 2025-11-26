<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $databaseName = config('database.connections.mysql.database');

        // Connexion temporaire sans base de données sélectionnée
        config(['database.connections.mysql.database' => null]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        // Création de la base de données si elle n'existe pas
        $query = "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        DB::statement($query);

        // Reconnexion avec la base de données
        config(['database.connections.mysql.database' => $databaseName]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    public function down(): void
    {
        // Ne pas supprimer la base de données en rollback
        // Pour éviter la perte de données accidentelle
    }
};
