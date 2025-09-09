<?php

namespace Database\Seeders;

use App\Models\Back\Catalog\CategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Company;

class CategorySeeder extends Seeder
{
    protected int $roots = 6;      // broj root kategorija
    protected int $children = 3;   // broj djece po rootu
    protected int $attachMin = 1;  // min kategorija po tvrtki
    protected int $attachMax = 3;  // max kategorija po tvrtki

    public function run(): void
    {
        $fakerHR = \Faker\Factory::create('hr_HR');
        $fakerEN = \Faker\Factory::create('en_US');

        // 1) Kreiraj hijerarhiju (roots + children)
        $allCategoryIds = [];

        for ($r = 0; $r < $this->roots; $r++) {
            $root = Category::create([ 'is_active' => true, 'clicks' => 0 ]);
            $this->seedCategoryTranslations($root->id, $fakerHR->unique()->words(2, true), $fakerEN->unique()->words(2, true), $fakerHR, $fakerEN);
            $this->attachDefaultImage($root, 'root');

            $allCategoryIds[] = $root->id;

            for ($c = 0; $c < $this->children; $c++) {
                $child = Category::create([ 'is_active' => true, 'clicks' => 0, 'parent_id' => $root->id ]);
                $this->seedCategoryTranslations($child->id, $fakerHR->unique()->words(2, true), $fakerEN->unique()->words(2, true), $fakerHR, $fakerEN);
                $this->attachDefaultImage($child, 'child');

                $allCategoryIds[] = $child->id;
            }
        }

        // 2) Spoji postojeće tvrtke na 1–3 kategorije (nasumično)
        $companyIds = Company::query()->pluck('id')->all();
        if (!empty($companyIds) && !empty($allCategoryIds)) {
            foreach ($companyIds as $companyId) {
                $pick = collect($allCategoryIds)->shuffle()->take(random_int($this->attachMin, $this->attachMax))->values()->all();
                DB::table('category_company')->upsert(
                    array_map(fn($cid) => ['category_id'=>$cid, 'company_id'=>$companyId], $pick),
                    uniqueBy: ['category_id','company_id']
                );
            }
        }
    }

    protected function seedCategoryTranslations(int $categoryId, string $hrName, string $enName, $fakerHR, $fakerEN): void
    {
        // HR
        CategoryTranslation::updateOrCreate(
            ['category_id'=>$categoryId, 'locale'=>'hr'],
            [
                'name'        => Str::headline($hrName),
                'slug'        => $this->uniqueLocalizedSlug('category_translations','hr',$hrName),
                'description' => $fakerHR->optional(70)->sentence(12),
            ]
        );

        // EN
        CategoryTranslation::updateOrCreate(
            ['category_id'=>$categoryId, 'locale'=>'en'],
            [
                'name'        => Str::headline($enName),
                'slug'        => $this->uniqueLocalizedSlug('category_translations','en',$enName),
                'description' => $fakerEN->optional(70)->sentence(12),
            ]
        );
    }

    protected function uniqueLocalizedSlug(string $table, string $locale, string $base): string
    {
        $slug = Str::slug($base);
        $try  = $slug;
        $i = 2;

        while (DB::table($table)->where('locale',$locale)->where('slug',$try)->exists()) {
            $try = $slug.'-'.$i++;
        }
        return $try;
    }

    protected function attachDefaultImage(Category $category, string $label): void
    {
        $default = public_path('theme1/assets/img/default_image.jpg');

        try {
            if (File::exists($default) && method_exists($category, 'addMedia')) {
                $category->addMedia($default)->preservingOriginal()->toMediaCollection('image');
                return;
            }

            if (! method_exists($category, 'addMedia')) {
                return; // model nema MediaLibrary – preskoči
            }

            // Monogram SVG (C + broj id-a)
            $letter = strtoupper(substr('C'.$category->id, 0, 1));
            $bg = ['#2563eb','#d4af37','#0ea5e9','#16a34a','#7c3aed','#111827'][$category->id % 6];
            $svg = $this->monogramSvg($letter, $bg, strtoupper($label));
            $tmp = storage_path('app/seed/category_'.$category->id.'.svg');
            File::ensureDirectoryExists(dirname($tmp));
            File::put($tmp, $svg);

            $category->addMedia($tmp)->usingFileName('image.svg')->toMediaCollection('image');
        } catch (\Throwable $e) {
            // swallow – seed ne smije pasti na assetu
        }
    }

    protected function monogramSvg(string $letter, string $bg, string $sub = ''): string
    {
        $sub = e($sub);
        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="512" height="320" viewBox="0 0 512 320" xmlns="http://www.w3.org/2000/svg">
  <rect width="512" height="320" rx="20" fill="{$bg}"/>
  <text x="50%" y="52%" text-anchor="middle" font-family="Inter,Segoe UI,Arial" font-size="160" fill="#fff" font-weight="800">{$letter}</text>
  <text x="50%" y="85%" text-anchor="middle" font-family="Inter,Segoe UI,Arial" font-size="28" fill="rgba(255,255,255,.9)" font-weight="600">{$sub}</text>
</svg>
SVG;
    }
}
