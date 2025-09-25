<?php

namespace Database\Seeders;

use App\Models\Back\Billing\Subscription;
use App\Models\Back\Catalog\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::query()->pluck('id')->all();

        // Ako nema kompanija, napravi par minimalnih
        if (empty($companies)) {
            $companies = collect(range(1, 8))->map(function ($i) {
                return Company::create([
                    'oib' => (string)fake()->unique()->numerify('###########'),
                    'email' => fake()->unique()->safeEmail(),
                    'street' => fake()->streetName(),
                    'street_no' => (string)fake()->buildingNumber(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'is_published' => fake()->boolean(70),
                    'is_link_active' => fake()->boolean(80),
                    'referrals_count' => fake()->numberBetween(0, 50),
                    'clicks' => fake()->numberBetween(0, 500),
                ])->id;
            })->all();
        }

        $plans   = ['default','starter','pro','business'];
        $periods = ['monthly','yearly'];
        $statuses = ['trialing','active','paused','canceled','expired'];

        // 20 subscriptions
        for ($i = 0; $i < 20; $i++) {
            $companyId = Arr::random($companies);
            $period = Arr::random($periods);
            $price  = $period === 'monthly' ? Arr::random([19.00, 25.00, 39.00]) : Arr::random([190.00, 249.00, 399.00]);
            $status = Arr::random($statuses);

            $starts = Carbon::now()->subMonths(rand(0, 12))->startOfMonth();
            $trialEnds = (clone $starts)->addDays(rand(0, 14));
            $nextRenewal = $period === 'monthly'
                ? (clone $starts)->addMonths(rand(1, 12))->startOfMonth()
                : (clone $starts)->addYears(rand(1, 2))->startOfMonth();

            $endsOn = null;
            $canceledAt = null;
            $isAuto = true;

            if (in_array($status, ['canceled','expired'])) {
                $isAuto = false;
                $canceledAt = Carbon::now()->subDays(rand(1, 120));
                $endsOn = (clone $canceledAt)->toDateString();
            } elseif ($status === 'paused') {
                $isAuto = false;
            }

            Subscription::create([
                'company_id'      => $companyId,
                'plan'            => Arr::random($plans),
                'period'          => $period,
                'price'           => $price,
                'currency'        => 'EUR',
                'status'          => $status,
                'is_auto_renew'   => $isAuto,
                'starts_on'       => $starts->toDateString(),
                'ends_on'         => $endsOn,
                'next_renewal_on' => $nextRenewal->toDateString(),
                'trial_ends_on'   => $trialEnds->toDateString(),
                'canceled_at'     => $canceledAt,
                'notes'           => fake()->boolean(25) ? fake()->sentence(8) : null,
            ]);
        }
    }
}
