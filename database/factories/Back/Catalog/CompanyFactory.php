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
}
