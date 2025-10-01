<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RequiredPagesSeeder extends Seeder
{

    public function run(): void
    {
        $path = database_path('seeders/data/pages.json');

        if ( ! File::exists($path)) {
            $this->command->error("Datoteka pages.json nije pronađena na: {$path}");

            return;
        }

        $pages = json_decode(File::get($path), true);

        if ( ! is_array($pages)) {
            $this->command->error("Greška kod parsiranja pages.json");

            return;
        }

        foreach ($pages as $page) {
            DB::transaction(function () use ($page) {
                // Unos u categories
                $categoryId = DB::table('categories')->insertGetId([
                    'group'      => 'pages',
                    'is_active'  => true,
                    'is_navbar'  => false,
                    'position'   => 0,
                    'parent_id'  => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach (['hr', 'en'] as $locale) {
                    DB::table('category_translations')->insert([
                        'category_id' => $categoryId,
                        'locale'      => $locale,
                        'name'        => $page['title'][$locale],
                        'description' => $page['body'][$locale] ?? null,
                        'link_url'    => null,
                        'slug'        => $page['slug'][$locale] ?? Str::slug($page['title'][$locale])
                    ]);
                }
            });
        }

        $this->command->info("Pages se uspješno seedale iz pages.json");
    }
}
