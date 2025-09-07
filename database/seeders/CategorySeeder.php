<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Back\Catalog\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Root kategorije
        $services = Category::firstOrCreate(['name' => 'Usluge']);
        $retail   = Category::firstOrCreate(['name' => 'Maloprodaja']);
        $it       = Category::firstOrCreate(['name' => 'IT']);

        // Djeca (NestedSet)
        $marketing = Category::firstOrCreate(['name' => 'Marketing']);
        $marketing->appendToNode($services)->save();

        $consulting = Category::firstOrCreate(['name' => 'Konzalting']);
        $consulting->appendToNode($services)->save();

        $ecommerce = Category::firstOrCreate(['name' => 'E-commerce']);
        $ecommerce->appendToNode($retail)->save();

        $software = Category::firstOrCreate(['name' => 'Softver']);
        $software->appendToNode($it)->save();

        $hosting = Category::firstOrCreate(['name' => 'Hosting']);
        $hosting->appendToNode($it)->save();
    }
}
