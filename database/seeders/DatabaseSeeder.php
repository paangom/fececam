<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'partenaire_name' => 'GENESYS',
            'identifiant' => 'genesys',
            'telephone' => '+221774246535',
            'email' => 'papa.ngom@genesys.sn',
            'key_partenaire' => 'z4gN6DLsvy9NcN4hBBzd7TXeRngtqs',
        ]);
    }
}
