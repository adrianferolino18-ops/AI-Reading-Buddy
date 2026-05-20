<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This injects your first reading passage into the database
        Module::create([
            'title' => 'The Resilience of Ecosystems',
            'body_text' => 'Ecosystems are incredibly resilient networks. When unexpected environmental changes occur, wildlife species rely on established behavioral protocols for survival. Climate mitigation efforts help lower vulnerable risk patterns across these habitats.'
        ]);
    }
}