<?php

namespace Database\Seeders;

use App\Models\Back\Marketing\BannerTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Back\Marketing\Banner;
use App\Models\Back\Marketing\BannerSchedule;

class BannersSeeder extends Seeder
{
    protected int $count = 8;

    public function run(): void
    {
        $fakerHR = \Faker\Factory::create('hr_HR');
        $fakerEN = \Faker\Factory::create('en_US');

        for ($i = 0; $i < $this->count; $i++) {
            $status = collect(['active','active','active','draft','archived'])->random();

            $banner = Banner::create([
                'status' => $status,
                'clicks' => 0,
            ]);

            // HR prijevod
            BannerTranslation::updateOrCreate(
                ['banner_id'=>$banner->id,'locale'=>'hr'],
                [
                    'title'  => $hrTitle = Str::headline($fakerHR->words(3, true)),
                    'slogan' => $fakerHR->optional(60)->sentence(8),
                    'url'    => null, // možeš kasnije ispuniti realnim linkom
                ]
            );

            // EN prijevod
            BannerTranslation::updateOrCreate(
                ['banner_id'=>$banner->id,'locale'=>'en'],
                [
                    'title'  => $enTitle = Str::headline($fakerEN->words(3, true)),
                    'slogan' => $fakerEN->optional(60)->sentence(8),
                    'url'    => null,
                ]
            );

            // Raspored ovisno o statusu
            $this->seedSchedule($banner, $status, $i);

            // Media: default jpg ili SVG fallback
            $this->attachDefaultImage($banner, $hrTitle ?? 'Promo');
        }
    }

    protected function seedSchedule(Banner $banner, string $status, int $i): void
    {
        $today = Carbon::today();

        if ($status === 'active') {
            BannerSchedule::create([
                'banner_id'  => $banner->id,
                'start_date' => $today->copy()->subDays(rand(0,7)),
                'end_date'   => $today->copy()->addDays(rand(7,30)),
                'position'   => ($i % 3) + 1, // 1..3
            ]);
        }
        elseif ($status === 'draft') {
            BannerSchedule::create([
                'banner_id'  => $banner->id,
                'start_date' => $today->copy()->addDays(rand(3,14)),
                'end_date'   => $today->copy()->addDays(rand(20,40)),
                'position'   => ($i % 3) + 1,
            ]);
        }
        else { // archived
            BannerSchedule::create([
                'banner_id'  => $banner->id,
                'start_date' => $today->copy()->subDays(rand(30,60)),
                'end_date'   => $today->copy()->subDays(rand(1,10)),
                'position'   => ($i % 3) + 1,
            ]);
        }
    }

    protected function attachDefaultImage(Banner $banner, string $title): void
    {
        $default = public_path('theme1/assets/img/default_image.jpg');

        try {
            if (File::exists($default) && method_exists($banner, 'addMedia')) {
                $banner->addMedia($default)->preservingOriginal()->toMediaCollection('image');
                return;
            }

            if (! method_exists($banner, 'addMedia')) {
                return; // model nema MediaLibrary – preskoči
            }

            // Fallback: “AD” SVG s naslovom
            $svg = $this->bannerSvg($title);
            $tmp = storage_path('app/seed/banner_'.$banner->id.'.svg');
            File::ensureDirectoryExists(dirname($tmp));
            File::put($tmp, $svg);

            $banner->addMedia($tmp)->usingFileName('image.svg')->toMediaCollection('image');
        } catch (\Throwable $e) {
            // swallow
        }
    }

    protected function bannerSvg(string $title): string
    {
        $title = e(Str::limit($title, 28));
        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="1024" height="360" viewBox="0 0 1024 360" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0ea5e9"/>
      <stop offset="100%" stop-color="#2563eb"/>
    </linearGradient>
    <filter id="s" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="3" stdDeviation="6" flood-color="#000" flood-opacity=".2"/>
    </filter>
  </defs>
  <rect width="1024" height="360" rx="18" fill="url(#g)"/>
  <text x="80" y="210" font-family="Inter,Segoe UI,Arial" font-size="140" fill="rgba(255,255,255,.15)" font-weight="900">AD</text>
  <text x="80" y="120" font-family="Inter,Segoe UI,Arial" font-size="28" fill="#fff" font-weight="700" filter="url(#s)">{$title}</text>
  <text x="80" y="160" font-family="Inter,Segoe UI,Arial" font-size="16" fill="rgba(255,255,255,.9)">companythrone</text>
</svg>
SVG;
    }
}
