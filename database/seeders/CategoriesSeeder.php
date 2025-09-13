<?php

namespace Database\Seeders;

use App\Models\Admin\Catalog\Category;
use App\Models\Admin\Catalog\CategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class CategoriesSeeder extends Seeder
{
    /**
     * Seed a compact tree per group, add EN/HR translations,
     * attach dummy media (if medialibrary is migrated), and rebuild nested set.
     *
     * Idempotent: translations are updateOrCreate'd by (category_id, locale).
     */
    public function run(): void
    {
        $this->wipeOldData();
        
        DB::transaction(function () {
            $groups = [
                'companies' => [
                    'Books' => ['Fiction', 'Non-fiction', 'Children'],
                    'Comics' => ['Manga', 'Graphic Novels'],
                    'Stationery' => [],
                ],
                'blog' => [
                    'News' => [],
                    'Guides' => [],
                ],
                'pages' => [
                    'About' => [],
                    'Contact' => [],
                ],
                'footer' => [
                    'Customer Service' => [],
                    'Terms & Privacy' => [],
                ],
            ];
            
            $position = 0;
            
            foreach ($groups as $group => $tree) {
                foreach ($tree as $parentEn => $children) {
                    $parent = $this->makeCategory(
                        group: $group,
                        titleEn: $parentEn,
                        titleHr: $this->hrTitle($parentEn),
                        position: $position += 10,
                        isNavbar: $group !== 'footer'
                    );
                    
                    foreach ($children as $childEn) {
                        $this->makeCategory(
                            group: $group,
                            titleEn: $childEn,
                            titleHr: $this->hrTitle($childEn),
                            position: $position += 10,
                            parent: $parent
                        );
                    }
                }
            }
            
            // compute _lft/_rgt from parent_id once
            Category::fixTree();
        });
    }
    
    protected function makeCategory(
        string $group,
        string $titleEn,
        ?string $titleHr = null,
        int $position = 0,
        ?Category $parent = null,
        bool $isNavbar = false,
        bool $isActive = true,
    ): Category {
        $slugEn = Str::slug($titleEn);
        $slugHr = Str::slug($titleHr ?? $titleEn);
        
        $cat = new Category([
            'group'      => $group,
            'is_active'  => $isActive,
            'is_navbar'  => $isNavbar,
            'position'   => $position,
            'parent_id'  => $parent?->id,
        ]);
        $cat->save();
        
        $this->upsertTranslation($cat, 'en', $titleEn, $slugEn);
        $this->upsertTranslation($cat, 'hr', $titleHr ?? $titleEn, $slugHr);
        
        $this->attachDummyMedia($cat, $slugEn);
        
        return $cat;
    }
    
    protected function upsertTranslation(Category $cat, string $locale, string $title, string $slug): void
    {
        // Matches your migration: seo_title, seo_description, seo_keywords (NOT meta_*)
        CategoryTranslation::updateOrCreate(
            ['category_id' => $cat->id, 'locale' => $locale],
            [
                'name'            => $title,
                'slug'             => $slug,
                'link_url'         => null,
                'description'      => "Sample {$locale} description for {$title}.",
                'seo_title'        => $title,
                'seo_description'  => "SEO description for {$title}.",
                'seo_keywords'     => "category, {$title}, sample",
                'seo_json'         => null,
            ]
        );
    }
    
    protected function attachDummyMedia(Category $cat, string $seed): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('media')) {
            return;
        }
        
        // optional local placeholders you can drop in:
        $localImage  = public_path('media/avatars/default_image.jpg');
        $localIcon   = public_path('');
        $localBanner = public_path('shop/seeders/category-banner.jpg');
        
        try {
            // try remote first
            $cat->addMediaFromUrl("https://picsum.photos/seed/{$seed}-image/800/600")
                ->toMediaCollection('image');
            
            $cat->addMediaFromUrl("https://picsum.photos/seed/{$seed}-icon/128/128")
                ->toMediaCollection('icon');
            
            $cat->addMediaFromUrl("https://picsum.photos/seed/{$seed}-banner/1200/360")
                ->toMediaCollection('banner');
        } catch (\Throwable $e) {
            // fallback to local files if they exist
            if (is_file($localImage)) {
                $cat->addMedia($localImage)->toMediaCollection('image');
            }
            if (is_file($localIcon)) {
                $cat->addMedia($localIcon)->toMediaCollection('icon');
            }
            if (is_file($localBanner)) {
                $cat->addMedia($localBanner)->toMediaCollection('banner');
            }
        }
    }
    
    
    protected function hrTitle(string $en): string
    {
        return match ($en) {
            'Books' => 'Knjige',
            'Fiction' => 'Beletristika',
            'Non-fiction' => 'Publicistika',
            'Children' => 'Dječje',
            'Comics' => 'Stripovi',
            'Manga' => 'Manga',
            'Graphic Novels' => 'Grafički romani',
            'Stationery' => 'Papirnica',
            'News' => 'Vijesti',
            'Guides' => 'Vodiči',
            'About' => 'O nama',
            'Contact' => 'Kontakt',
            'Customer Service' => 'Korisnička podrška',
            'Terms & Privacy' => 'Uvjeti i privatnost',
            default => $en,
        };
    }
    
    protected function wipeOldData(): void
    {
        // Delete media rows (and files) for Category only
        if (Schema::hasTable('media')) {
            SpatieMedia::query()
                ->where('model_type', Category::class)
                ->cursor()
                ->each(fn (SpatieMedia $m) => $m->delete());
        }
        
        // Truncate tables in FK-safe way
        Schema::disableForeignKeyConstraints();
        DB::table('category_translations')->truncate();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
