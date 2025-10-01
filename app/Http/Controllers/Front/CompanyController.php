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

        ]);

        $company = $this->currentDraft() ?? new Company();

        DB::transaction(function () use (&$company, $data) {
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
        $company   = $this->requireDraft();
        $payments  = $settings->paymentsActive(); // aktivne opcije iz DB (+fallback config)
        $selectedCode = $request->session()->get(self::S_PLAN);

        // ako nema payment opcija, preskoči na review
        if (empty($payments)) {
            $request->session()->forget(self::S_PLAN);
            return view('front.company-review', [
                'company'      => $company,
                'selectedPlan' => null,
            ]);
        }

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

        // odabrani plan iz sesije
        $code = $request->session()->get(self::S_PLAN);
        $plan = $code ? $settings->paymentByCode($code) : null;

        $paymentView = null;
        $paymentData = [];
        $selectedPlan = $plan; // za prikaz u tablici

        if ($plan && !empty($plan['code'])) {
            // povuci driver iz configa: settings.payments.providers.{code}.driver
            $providerConf = config('settings.payments.providers.' . $plan['code'], []);
            $driverFqcn   = $providerConf['driver'] ?? null;

            if ($driverFqcn && class_exists($driverFqcn) && method_exists($driverFqcn, 'frontBlade')) {
                // merge default config + DB data
                $provider = array_replace_recursive(
                    $driverFqcn::defaultConfig(),
                    (array) ($plan['data'] ?? [])
                );

                // pripremi kontekst
                $ctx = [
                    'order_id' => $company->id,
                    'total'    => $plan['price'] ?? 0,
                    'customer' => [
                        'firstname' => optional(auth()->user())->first_name ?? '',
                        'lastname'  => optional(auth()->user())->last_name ?? '',
                        'address'   => trim(($company->street ?? '').' '.($company->street_no ?? '')),
                        'city'      => $company->city ?? '',
                        'country'   => $company->state ?? 'HR',
                        'postcode'  => $company->postcode ?? '',
                        'phone'     => $company->phone ?? '',
                        'email'     => $company->email ?? (optional(auth()->user())->email ?? ''),
                    ],
                    'lang'   => app()->getLocale() === 'hr' ? 'HR' : 'EN',
                    // povratni URL-ovi (možeš prilagoditi na vlastite callback rute)
                    'return' => localized_route('companies.show', [
                        'companyBySlug' => $company->t_slug
                                           ?? (method_exists($company, 'translation') ? optional($company->translation(app()->getLocale()))->slug : null)
                                              ?? (string) $company->getKey(),
                    ]),
                    'cancel' => localized_route('companies.review'),
                ];

                $paymentView = $driverFqcn::frontBlade();

                // ako driver ima buildFrontData, koristi ga; inače očekuješ da si $paymentData već složio
                if (method_exists($driverFqcn, 'buildFrontData')) {
                    $paymentData = $driverFqcn::buildFrontData($provider, $ctx);
                } else {
                    // minimum koji WSPay blade očekuje (ako nema helpera)
                    $paymentData = array_merge([
                        'action'   => $provider['gateway'][($provider['test'] ?? 1) ? 'test' : 'live'] ?? '',
                        'shop_id'  => $provider['shop_id'] ?? '',
                    ], $ctx);
                }
            }
        }

        // “sažetak” info (order i payment objekti za tablicu)
        $order = (object)[
            'number'     => ($paymentData['order_id'] ?? ('CMP-'.str_pad($company->id, 6, '0', STR_PAD_LEFT))),
            'created_at' => now(),
        ];
        $payment = (object)[
            'amount'   => $paymentData['total'] ?? ($selectedPlan['price'] ?? null),
            'currency' => $selectedPlan['currency'] ?? 'EUR',
            'reference'=> $order->number,
            // 'receipt_url' => ...
        ];

        return view('front.company-success', compact(
            'company',
            'selectedPlan',
            'paymentView',
            'paymentData',
            'order',
            'payment'
        ));
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
