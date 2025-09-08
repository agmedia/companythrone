<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Back\Catalog\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // jednostavne root kategorije (po potrebi proširi children)
        $names = ['Građevina','IT usluge','Marketing','Zdravstvo','Prijevoz','Usluge','Trgovina','Ugostiteljstvo'];

        foreach ($names as $name) {
            // s medijem
            Category::factory()->withMedia()->create(['name' => $name])->saveAsRoot();

            for ($i = 0; $i < 3; $i++) {
                $root = Category::query()->where('is_active',1)->inRandomOrder()->first();
                Category::factory()->withMedia()->create(['name'=>'Podkategorija'])->appendToNode($root)->save();
            }
            // Ako želiš djecu:

        }
    }
}
