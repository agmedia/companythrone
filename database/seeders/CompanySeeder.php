<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Back\Catalog\Company;
use App\Models\Back\Catalog\Category;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::pluck('id')->all();

        Company::factory(20)->create()->each(function (Company $c) use ($cats) {
            $pick = collect($cats)->shuffle()->take(rand(1,3))->all();
            $c->categories()->sync($pick);
        });
    }
}
