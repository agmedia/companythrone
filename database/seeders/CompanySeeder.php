<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Back\Catalog\{Company, Level, Category};

class CompanySeeder extends Seeder
{

    public function run(): void
    {
        $levels = Level::pluck('id')->all();
        $cats   = Category::pluck('id')->all();

        Company::factory()
               ->count(20)
               ->withMedia()               // DODAJ LOGO
               ->create()
               ->each(function (Company $c) use ($levels, $cats) {
                   if ($levels) {
                       $c->level_id = $levels[array_rand($levels)];
                       $c->save();
                   }
                   // dodaj 1–3 kategorije
                   if ($cats) {
                       $attach = collect($cats)->shuffle()->take(rand(1, 3))->values()->all();
                       $c->categories()->syncWithoutDetaching($attach);
                   }
               });
    }
}

