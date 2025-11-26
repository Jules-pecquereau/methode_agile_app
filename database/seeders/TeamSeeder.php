<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optionally clear existing teams
        Team::truncate();


        // Create 7 additional random teams via factory
        Team::factory()->count(7)->create();
    }
}
