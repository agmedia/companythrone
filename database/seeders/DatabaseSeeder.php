<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Obavezni seeders
        $this->call([
            RoleSeeder::class,
            LevelSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            RequiredPagesSeeder::class,
        ]);

        // Pitaj korisnika samo u CLI kontekstu
        if (App::runningInConsole()) {
            if ($this->command->confirm('Želite li generirati dummy podatke?', false)) {
                $this->call([
                    // popis opcionalnih seedera
                    CompanySeeder::class,
                    CategorySeeder::class,
                    BannerSeeder::class,
                    UserSeeder::class,
                    // dodaj ovdje sve koje želiš
                ]);
            }
        }
    }
}
