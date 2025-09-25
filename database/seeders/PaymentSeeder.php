<?php

namespace Database\Seeders;

use App\Models\Back\Billing\Payment;
use App\Models\Back\Billing\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $subs = Subscription::query()->with('company')->get();
        if ($subs->isEmpty()) {
            $this->command->warn('No subscriptions found, skipping PaymentSeeder.');
            return;
        }

        $statuses = ['pending','paid','failed','expired'];
        $providers = ['stripe','paypal','bank'];
        $methods = ['card','bank','paypal'];

        // ~20 payments
        for ($i = 0; $i < 20; $i++) {
            $sub = $subs->random();
            $amount = $sub->price;
            $periodStart = Carbon::now()->subMonths(rand(0, 12))->startOfMonth();
            $periodEnd   = (clone $periodStart)->addMonths($sub->period === 'monthly' ? 1 : 12)->subDay();

            $status = Arr::random($statuses);
            $issued = (clone $periodStart)->subDays(rand(0, 5))->setTime(rand(8, 17), rand(0,59));
            $paidAt = $status === 'paid'
                ? (clone $issued)->addDays(rand(0, 10))->setTime(rand(8, 17), rand(0,59))
                : null;

            $vatRate = 25.00;
            $tax = round($amount * $vatRate / 100, 2);
            $net = round($amount - $tax, 2);

            $data = [
                'company_id'     => $sub->company_id,
                'subscription_id'=> $sub->id,
                'amount'         => $amount,
                'currency'       => 'EUR',
                'vat_rate'       => $vatRate,
                'tax_amount'     => $tax,
                'net_amount'     => $net,
                'status'         => $status,
                'period_start'   => $periodStart->toDateString(),
                'period_end'     => $periodEnd->toDateString(),
                'issued_at'      => $issued,
                'paid_at'        => $paidAt,
                'provider'       => Arr::random($providers),
                'provider_ref'   => strtoupper(Str::random(10)),
                'method'         => Arr::random($methods),
                'invoice_no'     => 'INV-'.date('Y').'-'.str_pad((string)rand(1, 99999), 5, '0', STR_PAD_LEFT),
            ];

            if (Schema::hasColumn('payments', 'meta')) {
                $data['meta'] = ['note' => fake()->sentence(6)];
            }

            Payment::create($data);
        }
    }
}
