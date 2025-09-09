<?php

namespace Database\Seeders;

use App\Models\Back\Catalog\CompanyTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Back\Catalog\Company;
use App\Models\Back\Catalog\Level;

class CompanySeeder extends Seeder
{
    /** koliko demo tvrtki */
    protected int $count = 30;

    public function run(): void
    {
        $fakerHR = \Faker\Factory::create('hr_HR');
        $fakerEN = \Faker\Factory::create('en_US');

        $levelIds = Level::query()->pluck('id')->all();
        if (empty($levelIds)) {
            $this->call(LevelsSeeder::class);
            $levelIds = Level::query()->pluck('id')->all();
        }

        // (opcionalno) očisti stare medijske fileove u storage/app/seed
        Storage::disk('local')->makeDirectory('seed');

        for ($i = 0; $i < $this->count; $i++) {

            // 1) Kreiraj baznu company
            $isPublished = $fakerHR->boolean(80);

            $company = Company::create([
                'level_id'        => $fakerHR->randomElement($levelIds),
                'oib'             => $this->uniqueOib(),
                'street'          => $fakerHR->streetName(),
                'street_no'       => (string) $fakerHR->numberBetween(1, 200),
                'city'            => $fakerHR->city(),
                'state'           => 'HR',
                'email'           => $fakerEN->unique()->safeEmail(),
                'phone'           => $fakerHR->optional()->phoneNumber(),
                'is_published'    => $isPublished,
                'is_link_active'  => false,
                'referrals_count' => $fakerHR->numberBetween(0, 7),
                'clicks'          => $fakerHR->numberBetween(0, 1500),
                'published_at'    => $isPublished ? now()->subDays($fakerHR->numberBetween(0, 60)) : null,
            ]);

            // 2) HR prijevod (obavezan)
            $hrName = $fakerHR->company();
            $hrSlug = $this->uniqueLocalizedSlug('company_translations', 'hr', $hrName);

            $hr = CompanyTranslation::updateOrCreate(
                ['company_id' => $company->id, 'locale' => 'hr'],
                [
                    'name'        => $hrName,
                    'slug'        => $hrSlug,
                    'slogan'      => $fakerHR->sentence(),
                    'description' => $fakerHR->paragraph(),
                ]
            );

            // 3) EN prijevod (uvijek generiramo za demo)
            $enName = $fakerEN->company();
            $enSlug = $this->uniqueLocalizedSlug('company_translations', 'en', $enName);

            $en = CompanyTranslation::updateOrCreate(
                ['company_id' => $company->id, 'locale' => 'en'],
                [
                    'name'        => $enName,
                    'slug'        => $enSlug,
                    'slogan'      => $fakerEN->sentence(),
                    'description' => $fakerEN->paragraph(),
                ]
            );

            // 4) Logo (default slika ili monogram SVG)
            $this->attachDefaultLogo($company, $hrName);
        }
    }

    /**
     * Generira jedinstveni OIB (11 znamenki) koji ne postoji u bazi.
     */
    protected function uniqueOib(): string
    {
        do {
            $oib = '';
            for ($i = 0; $i < 11; $i++) { $oib .= random_int(0,9); }
        } while (Company::where('oib', $oib)->exists());

        return $oib;
    }

    /**
     * Jedinstveni slug u translation tablici, unutar konkretnog jezika.
     */
    protected function uniqueLocalizedSlug(string $table, string $locale, string $base): string
    {
        $slug = Str::slug($base);
        $try = $slug;
        $i = 2;

        while (DB::table($table)->where('locale', $locale)->where('slug', $try)->exists()) {
            $try = $slug.'-'.$i++;
        }
        return $try;
    }

    /**
     * Pokušaj attachati default PNG; ako ga nema, izgeneriraj monogram SVG i attachaj.
     */
    protected function attachDefaultLogo(Company $company, string $name): void
    {
        // 1) pokušaj s default PNG-om iz teme
        $defaultPng = public_path('theme1/assets/img/default_image.jpg'); // kako si naveo
        if (File::exists($defaultPng)) {
            $company->addMedia($defaultPng)
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            return;
        }

        // 2) fallback: monogram SVG s prvim slovom naziva
        $letter = Str::upper(Str::substr(trim($name), 0, 1) ?: 'C');
        $bg     = $this->palette()[$company->id % 6];
        $svg    = $this->monogramSvg($letter, $bg);
        $tmp    = storage_path('app/seed/company_'.$company->id.'_logo.svg');

        File::put($tmp, $svg);
        $company->addMedia($tmp)
                ->usingFileName('logo.svg')
                ->toMediaCollection('logo');
    }

    /**
     * Jednostavan SVG monogram (256x256, krug + slovo).
     */
    protected function monogramSvg(string $letter, string $bg): string
    {
        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="256" height="256" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <filter id="s" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000" flood-opacity=".15"/>
    </filter>
  </defs>
  <circle cx="128" cy="128" r="112" fill="{$bg}" filter="url(#s)"/>
  <text x="128" y="155" font-family="Inter,Segoe UI,Arial" font-size="120" text-anchor="middle" fill="#fff" font-weight="700">{$letter}</text>
</svg>
SVG;
    }

    protected function palette(): array
    {
        // ugodne nijanse
        return ['#2563eb', '#d4af37', '#0ea5e9', '#16a34a', '#7c3aed', '#111827'];
    }
}
