<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Catalog\Category;
use App\Models\Back\Catalog\Company;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Back\Settings\Settings;

use App\Models\Back\Billing\Subscription;
use App\Models\Back\Billing\Payment;


class CompanyController extends Controller
{
    private const S_DRAFT = 'company_wizard.company_id';
    private const S_PLAN  = 'company_wizard.plan_code';

    /** STEP 1: FORM (GET /add-company) */
    public function create(Request $request)
    {
        $categories = Category::query()
                              ->forGroup('tvrtke')
                              ->where('is_active', true)
                              ->with('translations')
                              ->defaultOrder()
                              ->get();

        $company = $this->currentDraft(); // ako se user vratio na formu

        return view('front.company-create', compact('categories', 'company'));
    }

    /** STEP 1: SAVE DRAFT (POST /add-company) */
    public function store(Request $request)
    {
        // ⚠️ prilagodi po stvarnim inputima u company-create.blade.php
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'oib'         => ['nullable','string','max:20'],
            'email'       => ['nullable','email','max:255'],
            'phone'       => ['nullable','string','max:50'],
            'weburl'      => ['nullable','url','max:255'],
            'street'      => ['nullable','string','max:255'],
            'street_no'   => ['nullable','string','max:50'],
            'city'        => ['nullable','string','max:120'],
            'state'       => ['nullable','string','max:50'],
            'description' => ['nullable','string'],
            'categories'  => ['array'],
            'categories.*'=> ['integer','exists:categories,id'],
            'logo_file' => ['nullable','image','max:2048'],

        ]);

        $company = $this->currentDraft() ?? new Company();

        DB::transaction(function () use (&$company, $data, $request) {
            // osnovna polja
            $company->fill([
                'oib'          => $data['oib'] ?? null,
                'email'        => $data['email'] ?? null,
                'phone'        => $data['phone'] ?? null,
                'weburl'       => $data['weburl'] ?? null,
                'street'       => $data['street'] ?? null,
                'street_no'    => $data['street_no'] ?? null,
                'city'         => $data['city'] ?? null,
                'state'        => $data['state'] ?? null,
                'description'  => $data['description'] ?? null,
                'is_published' => false, // draft
                'user_id'      => auth()->id(),  // ✅ set from the session user
            ]);

            // prijevod (t_* polja) – osiguraj slug
            if (method_exists($company, 'translation')) {
                $locale = app()->getLocale();
                $t = $company->translation($locale) ?? $company->translations()->make(['locale' => $locale]);
                $t->name = $data['name'];

                if($data['description']){
                    $t->description = $data['description'];
                }

                if (empty($t->slug)) {
                    $t->slug = $this->generateUniqueCompanySlug($t->name, $locale, $company->id ?? null);
                }

                $company->save();
                // LOGO upload (nakon $company->save())
                if ($request->hasFile('logo_file')) {
                    $file = $request->file('logo_file');
                    if (!$file->isValid()) {
                        throw new \RuntimeException('Neispravan upload datoteke.');
                    }
                    // Ako u modelu imaš ->singleFile() za 'logo', stari će se automatski zamijeniti.
                    $company->addMediaFromRequest('logo_file')->toMediaCollection('logo');
                }
                $company->translations()->save($t);
            } else {
                // ako je ime na baznom modelu
                $company->name = $data['name'];
                if (empty($company->slug ?? null)) {
                    $company->slug = Str::slug($company->name) ?: (string) $company->id;
                }
                $company->save();
            }

            // kategorije (ako relacija postoji)
            if (isset($data['categories']) && method_exists($company, 'categories')) {
                $company->categories()->sync($data['categories']);
            }
        });

        $request->session()->put(self::S_DRAFT, $company->id);

        return redirect()->to(localized_route('companies.payment'));
    }

    /** STEP 2: CHOOSE PAYMENT (GET /add-payment) */
    public function payment(Request $request, SettingsManager $settings)
    {
        $company      = $this->requireDraft();
        $payments     = $settings->paymentsActive(); // provideri
        $selectedCode = $request->session()->get(self::S_PLAN);

        // Ako nema payment opcija, preskoči na review
        if (empty($payments)) {
            $request->session()->forget(self::S_PLAN);
            return view('front.company-review', [
                'company'      => $company,
                'selectedPlan' => null,
            ]);
        }

        // === Cijena iz configa (NETO) + PDV iz settings (JSON lista) ===
        $planConf = config('settings.payments.plans.default', [
            'price'    => 20.00,   // neto
            'currency' => 'EUR',
            'period'   => 'yearly',
            'tax_code' => 'tax',
        ]);

        // helper za PDV stopu (koristi onaj što smo dodali) ili quick inline dohvat:
        $vatRate = $this->getVatRate(); // npr. 25.0

        $net      = (float) ($planConf['price'] ?? 0);
        $gross    = round($net * (1 + $vatRate/100), 2);
        $currency = (string) ($planConf['currency'] ?? 'EUR');
        $period   = in_array(($planConf['period'] ?? 'yearly'), ['monthly','yearly'], true)
            ? $planConf['period'] : 'yearly';

        // Ubrizgaj display polja u svaki provider (listu $payments)
        $payments = collect($payments)->map(function ($p) use ($gross, $currency, $period) {
            $p['display_price_gross'] = $gross;     // npr. 25.00
            $p['display_currency']    = $currency;  // EUR
            $p['display_period']      = $period;    // 'yearly' ili 'monthly'
            return $p;
        })->all();

        return view('front.company-payment', compact('company', 'payments', 'selectedCode'));
    }


    /** STEP 3: REVIEW (POST /review) */
    public function review(Request $request, SettingsManager $settings)
    {
        $company  = $this->requireDraft();

        $payments = $settings->paymentsActive();
        $codes    = collect($payments)->pluck('code')->all();

        if (!empty($payments)) {
            $data = $request->validate([
                'plan' => ['required','string', Rule::in($codes)],
            ]);
            $request->session()->put(self::S_PLAN, $data['plan']);
            $selectedPlan = $settings->paymentByCode($data['plan']);
        } else {
            $request->session()->forget(self::S_PLAN);
            $selectedPlan = null;
        }

        return view('front.company-review', compact('company', 'selectedPlan'));
    }

    /**
     * CHECKOUT PREVIEW + PAYMENT FORM (POST /success)
     * - Ne objavljuje tvrtku.
     * - Rendera driverov Blade s pripremljenim $paymentData (npr. WSPay hidden polja i MD5).
     */


    public function success(Request $request, SettingsManager $settings)
    {
        $company = $this->requireDraft();

        // ============ 1) Odabrani PAYMENT PROVIDER iz sesije ============
        $providerCode = $request->session()->get(self::S_PLAN); // npr. 'wspay' ili 'bank'
        $selectedPlan = $providerCode ? $settings->paymentByCode($providerCode) : null;

        // ============ 2) Učitaj PLAN iz config/settings.php ============
        // Ako imaš više planova, promijeni 'default' u ključ koji želiš.
        $planConf  = config('settings.payments.plans.default', [
            'label'    => 'Godišnja pretplata',
            'price'    => 20.00,  // NETO
            'currency' => 'EUR',
            'period'   => 'yearly',
            'tax_code' => 'tax',
        ]);

        $netPrice = (float) ($planConf['price'] ?? 0);         // neto (bez PDV-a)
        $currency = (string) ($planConf['currency'] ?? 'EUR');
        $period   = (string) ($planConf['period'] ?? 'yearly'); // očekuje: 'yearly' ili 'monthly'

        // ============ 3) PDV stopa iz baze (settings.code = 'tax') ============
        $taxCode  = (string) ($planConf['tax_code'] ?? 'tax');
        $vatRate = $this->getVatRate(/* npr. 1 za HR */);

        // Neto → Bruto
        $gross    = round($netPrice * (1 + $vatRate / 100), 2);
        $net      = round($netPrice, 2);
        $tax      = round($gross - $net, 2);

        // ============ 4) Priprema payment driver view-a kao i do sad ============
        $paymentView = null;
        $paymentData = [];
        if ($selectedPlan && !empty($selectedPlan['code'])) {
            $providerConf = config('settings.payments.providers.' . $selectedPlan['code'], []);
            $driverFqcn   = $providerConf['driver'] ?? null;

            if ($driverFqcn && class_exists($driverFqcn) && method_exists($driverFqcn, 'frontBlade')) {
                // merge default config + DB data (iz SettingsManagera)
                $provider = array_replace_recursive(
                    $driverFqcn::defaultConfig(),
                    (array) ($selectedPlan['data'] ?? [])
                );

                $ctx = [
                    'order_id' => $company->id,
                    'total'    => $gross, // šaljemo BRUTO izračunato iz plana + PDV-a
                    'customer' => [
                        'firstname' => optional(auth()->user())->first_name ?? '',
                        'lastname'  => optional(auth()->user())->last_name ?? '',
                        'address'   => trim(($company->street ?? '') . ' ' . ($company->street_no ?? '')),
                        'city'      => $company->city ?? '',
                        'country'   => $company->state ?? 'HR',
                        'postcode'  => $company->postcode ?? '',
                        'phone'     => $company->phone ?? '',
                        'email'     => $company->email ?? (optional(auth()->user())->email ?? ''),
                    ],
                    'lang'   => app()->getLocale() === 'hr' ? 'HR' : 'EN',
                    'return' => localized_route('companies.show', [
                        'companyBySlug' => $company->t_slug
                            ?? (method_exists($company, 'translation') ? optional($company->translation(app()->getLocale()))->slug : null)
                                ?? (string) $company->getKey(),
                    ]),
                    'cancel' => localized_route('companies.review'),
                ];

                $paymentView = $driverFqcn::frontBlade();

                if (method_exists($driverFqcn, 'buildFrontData')) {
                    $paymentData = $driverFqcn::buildFrontData($provider, $ctx);
                } else {
                    $paymentData = array_merge([
                        'action'  => $provider['gateway'][($provider['test'] ?? 1) ? 'test' : 'live'] ?? '',
                        'shop_id' => $provider['shop_id'] ?? '',
                    ], $ctx);
                }
            }
        }

        // ============ 5) Spremi SUBSCRIPTION + PAYMENT (idempotentno) ============
        DB::transaction(function () use ($company, $providerCode, $gross, $net, $tax, $vatRate, $currency, $period) {
            $start     = \Carbon\Carbon::today();
            $periodEnd = $period === 'monthly'
                ? (clone $start)->addMonth()->subDay()
                : (clone $start)->addYear()->subDay(); // default yearly
            $nextRen   = $period === 'monthly'
                ? (clone $start)->addMonth()
                : (clone $start)->addYear();

            // Subscription: cijena = BRUTO (što stvarno plaća korisnik)
            $subscription = \App\Models\Back\Billing\Subscription::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'plan'       => 'default',               // ako imaš više planova, zamijeni ključem
                    'period'     => $period === 'monthly' ? 'monthly' : 'yearly',
                    'starts_on'  => $start->toDateString(),
                ],
                [
                    'price'           => $gross,             // BRUTO
                    'currency'        => $currency,
                    'status'          => 'active',
                    'is_auto_renew'   => 1,
                    'ends_on'         => null,               // open-ended dok se ne otkaže
                    'next_renewal_on' => $nextRen->toDateString(),
                    'trial_ends_on'   => null,
                ]
            );

            // Payment – pokriva 1 period od starta
            \App\Models\Back\Billing\Payment::firstOrCreate(
                [
                    'company_id'      => $company->id,
                    'subscription_id' => $subscription->id,
                    'status'          => 'pending',
                    'period_start'    => $start->toDateString(),
                    'period_end'      => $periodEnd->toDateString(),
                ],
                [
                    'amount'       => $gross,               // BRUTO
                    'vat_rate'     => $vatRate,             // npr. 25.00
                    'tax_amount'   => $tax,
                    'net_amount'   => $net,
                    'currency'     => $currency,
                    'issued_at'    => now(),
                    'paid_at'      => null,
                    'provider'     => $providerCode,        // 'wspay', 'bank', ...
                    'method'       => $providerCode === 'bank' ? 'bank' : 'card',
                    'provider_ref' => null,
                    'invoice_no'   => null,
                    'meta'         => null,
                ]
            );
        });

        // ============ 6) “Sažetak” za prikaz ============
        $order = (object)[
            'number'     => ($paymentData['order_id'] ?? ('CMP-' . str_pad($company->id, 6, '0', STR_PAD_LEFT))),
            'created_at' => now(),
        ];
        $payment = (object)[
            'amount'   => $paymentData['total'] ?? $gross, // prikaži bruto
            'currency' => $currency,
            'reference'=> $order->number,
        ];

        return view('front.company-success', [
            'company'      => $company,
            'selectedPlan' => $selectedPlan, // provider info za prikaz
            'paymentView'  => $paymentView,
            'paymentData'  => $paymentData,
            'order'        => $order,
            'payment'      => $payment,
        ]);
    }


    private function getVatRate(?int $geoZoneId = null): float
    {
        $row = Settings::query()
            ->where('code', 'tax')
            ->where('key', 'list')
            ->where('json', 1)
            ->latest('id')
            ->first();

        if (!$row) {
            return 25.0; // fallback
        }

        $items = json_decode($row->value ?? '[]', true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($items)) {
            return 25.0;
        }

        // prioritet: aktivna stavka koja se poklapa s geo_zonom (ako je zadana),
        // inače prva aktivna
        $active = collect($items)
            ->filter(fn($it) => !empty($it['status']))
            ->when($geoZoneId, fn($c) => $c->where('geo_zone', $geoZoneId))
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->first();

        $rate = isset($active['rate']) ? (float)$active['rate'] : 25.0;

        return $rate > 0 ? $rate : 25.0;
    }


    /* ================= Helpers ================= */

    private function currentDraft(): ?Company
    {
        $id = session(self::S_DRAFT);
        return $id ? Company::query()->find($id) : null;
    }

    private function requireDraft(): Company
    {
        $draft = $this->currentDraft();
        abort_if(!$draft, 302, '', ['Location' => localized_route('companies.create')]);
        return $draft;
    }

    /** Jedinstveni slug za company_translations (po jeziku) */
    private function generateUniqueCompanySlug(string $name, string $locale, ?int $ignoreCompanyId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'company';
        }

        $slug = $base;
        $i = 2;

        $exists = function (string $candidate) use ($locale, $ignoreCompanyId) {
            return DB::table('company_translations')
                     ->where('locale', $locale)
                     ->where('slug', $candidate)
                     ->when($ignoreCompanyId, fn($q) => $q->where('company_id', '<>', $ignoreCompanyId))
                     ->exists();
        };

        while ($exists($slug)) {
            $slug = $base . '-' . $i;
            $i++;
            if ($i > 200) { // safety
                $slug = $base . '-' . Str::random(4);
                break;
            }
        }

        return $slug;
    }
}
