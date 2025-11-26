<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Définir les permissions du manager
        Bouncer::allow('manager')->to('manage-tasks');

        // Création du Manager
        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@app.com',
            'password' => bcrypt('password'),
        ]);
        $manager->assign('manager');

        // Création de l'Utilisateur standard
        User::factory()->create([
            'name' => 'Utilisateur',
            'email' => 'user@app.com',
            'password' => bcrypt('password'),
        ]);

        // Création des équipes
        Team::create(['name' => 'Équipe Alpha']);
        Team::create(['name' => 'Équipe Beta']);
        Team::create(['name' => 'Équipe Gamma']);
    }
}
