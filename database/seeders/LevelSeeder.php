<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Back\Catalog\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1,5) as $n) {
            Level::firstOrCreate(['number' => $n], ['description' => "Level $n"]);
        }
    }
}
