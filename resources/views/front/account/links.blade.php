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
                    <div class="col-md-8">
                        <input name="url" type="email" class="form-control" placeholder="{{ __('Email osobe koju želiš pozvati') }}" required>
                    </div>
                    <div class="col-md-3">
                        <input name="label" type="text" class="form-control" placeholder="{{ __('Opis (opcionalno)') }}">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary" @disabled($referralCount >= $referralRequired)>{{ __('Dodaj') }}</button>
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
                                    <div class="fw-semibold">{{ $ref->label ?? __('Bez naziva') }}</div>
                                    <a href="{{ $ref->url }}" target="_blank" class="small">{{ $ref->url }}</a>
                                </div>
                                <div class="text-muted small">
                                    {{ __('Klikovi:') }} {{ $ref->clicks }}
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">{{ __('Još nema preporuka.') }}</li>
                        @endforelse
                    </ul>
                </div>

                <div class="mb-4 mt-5 ">
                    <h5 class="fw-semibold">{{ __('Dnevni zadaci') }}</h5>
                    <div class="small text-muted mb-2">
                        {{ __('Dodajte barem ') . $limitPerDay . __(' klikova da biste objavili svoj link.)') }}
                        <br>
                        {{ __('Imate:') }} <span id="today-clicks">{{ $todayClicks }}</span> / {{ $limitPerDay }}
                    </div>

                    @php
                        // normaliziraj slots u niz intova
                        $slots = [];
                        if (!empty($usedSlots?->toArray())) {
                            $slots = array_map('intval', $usedSlots->toArray());
                        }
                    @endphp

                    <ul class="list-group mb-3" id="tasks-list">
                        @forelse($targets as $i => $target)
                            @php
                                // FIKSNI slot po indeksu (1-based)
                                $slot = $i + 1;
                                $done = in_array($slot, $slots, true);
                            @endphp

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge me-2 {{ $done ? 'bg-success' : 'bg-secondary' }}" data-slot-badge="{{ $slot }}">{{ $slot }}</span>
                                    {{ $target->t_name ?? '—' }}
                                </div>

                                <a href="{{ $target->weburl ?? '#' }}"
                                   class="btn btn-sm {{ $done ? 'btn-success disabled' : 'btn-outline-primary' }} task-btn"
                                   data-slot="{{ $slot }}"
                                   @if($done) aria-disabled="true" tabindex="-1" @endif
                                   @if(!empty($target?->id)) data-company="{{ $target->id }}" @endif>
                                    {{ $done ? __('Odrađeno') : __('Posjeti') }}
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">
                                {{ __('Trenutno nema dostupnih ciljeva.') }}
                            </li>
                        @endforelse
                    </ul>
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
                            b.setAttribute('aria-disabled', 'true');
                            b.setAttribute('tabindex', '-1');
                        }
                    });
                }
            }

            function markButtonDone(btn) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success', 'disabled');
                btn.textContent = "{{ __('Odrađeno') }}";
                btn.setAttribute('aria-disabled', 'true');
                btn.setAttribute('tabindex', '-1');
            }

            document.querySelectorAll('.task-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (btn.classList.contains('disabled')) return;

                    const companyId = btn.dataset.company || null;
                    const targetUrl = btn.getAttribute('href');

                    btn.classList.add('disabled'); // spriječi dvoklik

                    try {
                        const res = await fetch(clickEndpoint, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": csrf
                            },
                            body: JSON.stringify({
                                target_company_id: companyId
                            })
                        });

                        const data = await res.json().catch(() => ({}));

                        if (res.ok && data && data.success) {
                            // označi ovaj gumb kao odrađen
                            markButtonDone(btn);

                            // ažuriraj brojač iz backend-a
                            if (typeof data.todayClicks !== 'undefined') {
                                setTodayClicks(data.todayClicks);
                            } else {
                                // fallback
                                setTodayClicks((parseInt(clicksEl.textContent, 10) || 0) + 1);
                            }

                            // opcionalno: sakrij već odrađene targete iz liste
                            if (Array.isArray(data.visitedCompanyIds)) {
                                // ako želiš visual refresh: sakrij li element
                                const li = btn.closest('li');
                                if (li) li.style.opacity = '0.5';
                            }

                            // otvori link u novom tabu
                            if (targetUrl && targetUrl !== '#') {
                                window.open(targetUrl, '_blank', 'noopener');
                            }

                            disableAllIfLimitReached();
                        } else {
                            // vrati u početno stanje ako nije prošlo
                            btn.classList.remove('disabled');
                            btn.removeAttribute('aria-disabled');
                            btn.removeAttribute('tabindex');
                        }
                    } catch (err) {
                        btn.classList.remove('disabled');
                        btn.removeAttribute('aria-disabled');
                        btn.removeAttribute('tabindex');
                    }
                });
            });
        });
    </script>
@endpush

