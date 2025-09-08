<?php

namespace Database\Factories\Back\Catalog;

use App\Models\Back\Catalog\Company;
use App\Models\Back\Catalog\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{

    protected $model = Company::class;


    public function definition(): array
    {
        return [
            'level_id'        => Level::inRandomOrder()->value('id') ?? 1,
            'name'            => $this->faker->unique()->company(),
            'oib'             => (string) $this->faker->numberBetween(10000000, 99999999),
            'email'           => $this->faker->unique()->companyEmail(),
            'street'          => $this->faker->streetName(),
            'street_no'       => (string) $this->faker->buildingNumber(),
            'city'            => $this->faker->city(),
            'state'           => $this->faker->country(),
            'phone'           => $this->faker->phoneNumber(),
            'is_published'    => true,
            'is_link_active'  => false,
            'referrals_count' => 0,
            'published_at'    => now(),
        ];
    }


    public function withMedia(): static
    {
        return $this->afterCreating(function (Company $c) {
            $initial = mb_strtoupper(mb_substr($c->name, 0, 1));
            $svg     = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512">
  <rect width="100%" height="100%" fill="#0d6efd"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
        font-size="240" font-family="Arial, Helvetica, sans-serif" fill="#ffffff">{$initial}</text>
</svg>
SVG;
            $dir     = storage_path('app/tmp');
            if ( ! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $path = $dir . '/logo_' . $c->id . '.svg';
            file_put_contents($path, $svg);

            try {
                $c->addMedia($path)->preservingOriginal()->toMediaCollection('logo');
            } finally {
                @unlink($path);
            }
        });
    }
}
