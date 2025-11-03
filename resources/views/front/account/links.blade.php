@extends('layouts.app')
@section('title', __('Moji linkovi'))
@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">

                <h1 class="h4 mb-3">{{ __('Moji linkovi') }}</h1>

                <form method="post" action="{{ localized_route('account.links.store') }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-6">
                        <input name="url" type="email" class="form-control" placeholder="{{ __('Email osobe koju želiš pozvati') }}" required>
                    </div>
                    <div class="col-md-5">
                        <input name="title" type="text" class="form-control" placeholder="{{ __('Ime i prezime, naziv tvrtke') }}">
                    </div>
                    <div class="col-md-6">
                        <input name="phone" type="text" class="form-control" placeholder="{{ __('Broj telefona, mobitela') }}">
                    </div>
                    <div class="col-md-5">
                        <input name="label" type="text" class="form-control" placeholder="{{ __('Kratki opis ili komentar. (opcionalno)') }}">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary" >{{ __('Dodaj') }}</button>
                    </div>
                    {{-- <div class="col-12 text-muted small">
                        {{ __('Danas:') }} {{ $todayLinks }} / {{ $limitPerDay }}
                    </div> --}}
                </form>

                <div class="mt-5">
                    <h5 class="fw-semibold">{{ __('Moje preporuke') }}</h5>

                    <div class="small text-muted mb-2">
                        {{ __('Dodajte barem ') . $referralRequired . __(' preporuka da biste aktivirali svoj link.)') }}
                        <br>
                        {{ __('Imate:') }} {{ $referralCount }} / {{ $referralRequired }}
                    </div>

                    <ul class="list-group">
                        @forelse($referrals as $ref)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-3">
                                    <div class="fw-normal">Kontakt osoba: {{ $ref->title ?? __('NIje upisano') }} </div>
                                    <div class="fw-normal">Kontakt telefon: {{ $ref->phone ?? __('Nije upisano') }} </div>
                                    <div class="fw-normal">Komentar: {{ $ref->label ?? __('Bez naziva') }}  </div>
                                    <a href="{{ $ref->url }}" target="_blank" class="small">{{ $ref->url }}</a>
                                </div>
                                <div class="text-muted">
                                    <span class="small">{{ __('Iskorišten:') }}</span>
                                    @if($ref->clicks)
                                        <i class="fi-check-shield text-success"></i>
                                    @else
                                        <i class="fi-arrow-down text-warning"></i>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">{{ __('Još nema preporuka.') }}</li>
                        @endforelse
                    </ul>
                </div>

                <div class="mb-4 mt-5 ">
                    <h5 class="fw-semibold">{{ __('Dnevni zadaci') }}</h5>

                    @if ($referralActiveCount > 5)
                        <div class="small text-muted mb-2">
                            {{ __('Dodajte barem ') . $limitPerDay . __(' klikova da biste objavili svoj link.)') }}
                            <br>
                            {{ __('Imate:') }} <span id="today-clicks">{{ $todayClicks }}</span> / {{ $limitPerDay }}
                        </div>

                        @php
                            // posjećene kompanije danas (kontroler šalje visitedCompanyIds)
                            $visitedIds = collect($visitedCompanyIds ?? [])->map(fn($v) => (int)$v)->all();
                        @endphp

                        <ul class="list-group mb-3" id="tasks-list">
                            @forelse($targets as $i => $target)
                                @php
                                    $slot = $i + 1; // samo redni broj za prikaz
                                    $done = in_array($target->id, $visitedIds, true);
                                @endphp

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3> <span class="badge me-2 {{ $done ? 'bg-success' : 'bg-secondary' }}">{{ $slot }}</span></h3>
                                        {{-- parse_url($target->weburl, PHP_URL_HOST) ?? '—' --}}
                                    </div>

                                    <a href="{{ $target->weburl ?? '#' }}"
                                       class="btn btn-lg {{ $done ? 'btn-success disabled' : 'btn-outline-primary' }} task-btn"
                                       data-company="{{ $target->id }}"
                                       @if($done) aria-disabled="true" tabindex="-1" @endif>
                                        {{ $done ? __('Odrađeno') : __('Posjeti') }}
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">
                                    {{ __('Trenutno nema dostupnih ciljeva.') }}
                                </li>
                            @endforelse
                        </ul>
                    @else
                        <div class="small text-muted mb-2">
                            {{ __('Aktivirajte barem ') . $referralRequired . __(' linkova da biste objavili svoj link.)') }}
                            <br>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const clicksEl = document.getElementById('today-clicks');
            const limitPerDay = {{ (int) $limitPerDay }};
            const csrf = "{{ csrf_token() }}";
            const clickEndpoint = "{{ localized_route('account.links.click') }}";

            function setTodayClicks(n) {
                const v = Math.max(0, Math.min(parseInt(n||0,10), limitPerDay));
                clicksEl.textContent = v;
            }

            function disableAllIfLimitReached() {
                const v = parseInt(clicksEl.textContent, 10) || 0;
                if (v >= limitPerDay) {
                    document.querySelectorAll('.task-btn').forEach(b => {
                        if (!b.classList.contains('disabled')) {
                            b.classList.add('disabled');
                            b.setAttribute('aria-disabled','true');
                            b.setAttribute('tabindex','-1');
                        }
                    });
                }
            }

            function markDone(btn) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success','disabled');
                btn.textContent = "{{ __('Odrađeno') }}";
                btn.setAttribute('aria-disabled','true');
                btn.setAttribute('tabindex','-1');
            }

            document.querySelectorAll('.task-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (btn.classList.contains('disabled')) return;

                    const companyId = btn.dataset.company;
                    const targetUrl = btn.getAttribute('href');

                    // spriječi dvoklik
                    btn.classList.add('disabled');

                    try {
                        const res = await fetch(clickEndpoint, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": csrf
                            },
                            body: JSON.stringify({ target_company_id: companyId }) // 👈 bez slota!
                        });

                        // 419 / 302 fallback (npr. istekla CSRF sesija / redirect)
                        if (res.status === 419 || res.type === 'opaqueredirect') {
                            window.location.reload();
                            return;
                        }

                        const data = await res.json().catch(() => ({}));

                        if (res.ok && data && data.success) {
                            markDone(btn);

                            if (typeof data.todayClicks !== 'undefined') {
                                setTodayClicks(data.todayClicks);
                            } else {
                                setTodayClicks((parseInt(clicksEl.textContent,10) || 0) + 1);
                            }

                            if (targetUrl && targetUrl !== '#') {
                                window.open(targetUrl, '_blank', 'noopener');
                            }

                            disableAllIfLimitReached();
                        } else {
                            // vrati gumb ako server nije prihvatio klik
                            btn.classList.remove('disabled');
                            btn.removeAttribute('aria-disabled');
                            btn.removeAttribute('tabindex');
                            console.warn('Click rejected:', data);
                        }
                    } catch (err) {
                        btn.classList.remove('disabled');
                        btn.removeAttribute('aria-disabled');
                        btn.removeAttribute('tabindex');
                        console.error('Network error:', err);
                    }
                });
            });
        });
    </script>
@endpush
