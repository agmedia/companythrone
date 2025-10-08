<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Catalog\Category;
use App\Models\Back\Catalog\Company;
use App\Services\Settings\SettingsManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Back\Settings\Settings;

use App\Models\Back\Billing\Subscription;
use App\Models\Back\Billing\Payment;

class CompanyController extends Controller
{

    private const S_DRAFT  = 'company_wizard.company_id';
    private const S_PLAN   = 'company_wizard.plan_code';
    private const S_FINISH = 'company_wizard.finish';


    /** STEP 1: FORM (GET /add-company) */
    public function create(Request $request)
    {
        $company = $this->currentDraft(); // ako se user vratio na formu

        return view('front.company-create', compact('company'));
    }


    /** STEP 1: SAVE DRAFT (POST /add-company) */
    public function store(Request $request)
    {
        // ⚠️ prilagodi po stvarnim inputima u company-create.blade.php
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'oib'         => ['required', 'string', 'max:20'],
            'email'       => ['required', 'email', 'max:255'],
            'weburl'      => ['nullable', 'url', 'max:255'],
            'street'      => ['required', 'string', 'max:255'],
            'street_no'   => ['required', 'string', 'max:50'],
            'city'        => ['required', 'string', 'max:120'],
            'state'       => ['nullable', 'string', 'max:50'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'logo_file'   => ['nullable', 'image', 'max:2048'],

        ]);

        $company = $this->currentDraft() ?? new Company();
        $user    = auth()->user();

        DB::transaction(function () use (&$company, $data, $request, $user) {
            // osnovna polja
            $company->fill([
                'oib'            => $data['oib'] ?? null,
                'email'          => $data['email'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'weburl'         => $data['weburl'] ?? null,
                'street'         => $data['street'] ?? null,
                'street_no'      => $data['street_no'] ?? null,
                'city'           => $data['city'] ?? null,
                'state'          => $data['state'] ?? null,
                'description'    => htmlspecialchars(trim($data['description'])) ?? null,
                'is_published'   => false, // draft
                'is_link_active' => false,
                'user_id'        => $user->id,  // ✅ set from the session user
            ]);

            // prijevod (t_* polja) – osiguraj slug
            if (method_exists($company, 'translation')) {
                $locale  = app()->getLocale();
                $t       = $company->translation($locale) ?? $company->translations()->make(['locale' => $locale]);
                $t->name = $data['name'];

                if ($data['description']) {
                    $t->description = $data['description'];
                }

                if (empty($t->slug)) {
                    $t->slug = $this->generateUniqueCompanySlug($t->name, $locale, $company->id ?? null);
                }

                $company->save();
                // LOGO upload (nakon $company->save())
                if ($request->hasFile('logo_file')) {
                    $file = $request->file('logo_file');
                    if ( ! $file->isValid()) {
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

            $user->assignRole('company_owner');

            // kategorije (ako relacija postoji)
            /*if (isset($data['categories']) && method_exists($company, 'categories')) {
                $company->categories()->sync($data['categories']);
            }*/
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

            return view('front.company-create');
        }

        // === Cijena iz configa (NETO) + PDV iz settings (JSON lista) ===
        $planConf = config('settings.payments.plans.default');

        // helper za PDV stopu (koristi onaj što smo dodali) ili quick inline dohvat:
        $vatRate = vat_rate(); // npr. 25.0

        $net      = (float) ($planConf['price'] ?? 0);
        $gross    = round($net * (1 + $vatRate / 100), 2);
        $currency = (string) ($planConf['currency'] ?? 'EUR');
        $period   = in_array(($planConf['period'] ?? 'yearly'), ['monthly', 'yearly'], true)
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
        $company = $this->requireDraft();

        $payments = $settings->paymentsActive();
        $codes    = collect($payments)->pluck('code')->all();

        if ( ! empty($payments)) {
            $data = $request->validate([
                'plan' => ['required', 'string', Rule::in($codes)],
            ]);
            $request->session()->put(self::S_PLAN, $data['plan']);
            $selectedPlan = $settings->paymentByCode($data['plan']);

            // ============ 2) Učitaj PLAN iz config/settings.php ============
            // Ako imaš više planova, promijeni 'default' u ključ koji želiš.
            $planConf = config('settings.payments.plans.default');

            $netPrice = (float) (Subscription::getPrice($selectedPlan['price']) ?? 0);         // neto (bez PDV-a)
            $currency = (string) ($selectedPlan['currency'] ?? 'EUR');
            $period   = (string) ($planConf['period'] ?? 'yearly'); // očekuje: 'yearly' ili 'monthly'

            // ============ 3) PDV stopa iz baze (settings.code = 'tax') ============
            $vatRate = vat_rate(/* npr. 1 za HR */);

            // Neto → Bruto
            $gross = round($netPrice * (1 + $vatRate / 100), 2);

            $subscription = Subscription::query()
                                        ->where('company_id', $company->id)
                                        ->where('status', 'active')
                                        ->first();

            if ( ! $subscription) {
                $start   = Carbon::today();
                $nextRen = $period === 'monthly'
                    ? (clone $start)->addMonth()
                    : (clone $start)->addYear();

                $subscription = Subscription::query()->create([
                    'company_id'      => $company->id,
                    'plan'            => 'default',               // ako imaš više planova, zamijeni ključem
                    'period'          => $period,
                    'price'           => $gross,             // BRUTO
                    'currency'        => $currency,
                    'status'          => 'active',
                    'is_auto_renew'   => 0,
                    'starts_on'       => $start->toDateString(),
                    'ends_on'         => null,               // open-ended dok se ne otkaže
                    'next_renewal_on' => $nextRen->toDateString(),
                    'trial_ends_on'   => null,
                ]);
            }

            // ============ 4) Priprema payment driver view-a kao i do sad ============
            $paymentView = null;
            $paymentData = [];
            if ($selectedPlan && ! empty($selectedPlan['code'])) {
                $providerConf = config('settings.payments.providers.' . $selectedPlan['code'], []);
                $driverFqcn   = $providerConf['driver'] ?? null;

                if ($driverFqcn && class_exists($driverFqcn) && method_exists($driverFqcn, 'frontBlade')) {
                    // merge default config + DB data (iz SettingsManagera)
                    $provider                   = collect($payments)->where('code', $selectedPlan['code'])->first();
                    $data                       = $subscription->with('company')->get()->toArray();
                    $data[0]['company']['name'] = $company->t_name;



                    $paymentData = $driverFqcn::buildFrontData($data);

                    $paymentView = $driverFqcn::frontBlade();
                }
            }

        } else {
            $request->session()->forget(self::S_PLAN);
            abort(302, '', ['Location' => localized_route('companies.payment')]);
        }

        //dd($subscription->toArray(), $paymentData, $selectedPlan);

        return view('front.company-review', [
            'company'      => $company,
            'subscription' => $subscription,
            'selectedPlan' => $selectedPlan,
            'paymentView'  => $paymentView,
            'paymentData'  => $paymentData,
        ]);
    }


    public function order(Request $request, SettingsManager $settings)
    {
        if ( ! $request->has('provjera')) {
            abort(404);
        }

        $company      = $this->requireDraft();
        $providerCode = $request->session()->get(self::S_PLAN); // npr. 'wspay' ili 'bank'
        $selectedPlan = $providerCode ? $settings->paymentByCode($providerCode) : null;
        $validated    = null;

        if ($selectedPlan && ! empty($selectedPlan['code'])) {
            $providerConf = config('settings.payments.providers.' . $selectedPlan['code'], []);
            $driverFqcn   = $providerConf['driver'] ?? null;

            if ($driverFqcn && class_exists($driverFqcn) && method_exists($driverFqcn, 'validateResponse')) {
                $validated = $driverFqcn::validateResponse($request, $company);
            }
        }

        if ( ! $validated) {
            abort(404);
        }

        if ($validated['message'] === 'error') {
            $request->session()->put(self::S_FINISH, [
                'status'       => 'error',
                'company'      => $company,
                'subscription' => $validated['subscription'],
                'payment'      => $validated['payment'],
                'selectedPlan' => $selectedPlan,
            ]);

            return redirect()->to(localized_route('companies.error'));
        }

        $request->session()->put(self::S_FINISH, [
            'status'       => 'success',
            'company'      => $company,
            'subscription' => $validated['subscription'],
            'payment'      => $validated['payment'],
            'selectedPlan' => $selectedPlan,
            'qr'           => $validated['qr_code'],
        ]);

        return redirect()->to(localized_route('companies.success'));
    }


    /**
     * CHECKOUT PREVIEW + PAYMENT FORM (POST /success)
     * - Ne objavljuje tvrtku.
     * - Rendera driverov Blade s pripremljenim $paymentData (npr. WSPay hidden polja i MD5).
     */

    public function success(Request $request, SettingsManager $settings)
    {
        dd($request->session()->get(self::S_FINISH));

        return view('front.company-uspjeh');
    }


    public function error(Request $request)
    {
        dd($request->all());

        return view('front.company-greska');
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

        if ( ! $draft) {
            $draft = Company::query()->where('user_id', auth()->id())->first();
        }

        abort_if(! $draft, 302, '', ['Location' => localized_route('companies.create')]);

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
        $i    = 2;

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
