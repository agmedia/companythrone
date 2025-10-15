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
                // dekodiraj slots samo ako postoji session i payload
                $slots = [];
                if (!empty($usedSlots->toArray())) {
                $slots = $usedSlots->toArray() ?: [];
                }
                @endphp

                <ul class="list-group mb-3" id="tasks-list">
                    @forelse($targets as $i => $target)
                    @php
                    // trenutačna implementacija: svi redovi dobiju isti slot (next),
                    // no JS će ga nakon prvog klika povećavati bez refreša
                    $slot = $todayClicks + 1;
                    $done = in_array($slot, $slots, true);
                    @endphp

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge me-2 {{ $done ? 'bg-success' : 'bg-secondary' }}" data-slot-badge>{{ $slot }}</span>
                            {{ $target->t_name ?? '—' }}
                        </div>

                        <a href="{{ $target->weburl ?? '#' }}"
                           class="btn btn-sm {{ $done ? 'btn-success disabled' : 'btn-outline-primary task-btn' }}"
                           data-slot="{{ $slot }}"
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

        // Globalno držimo sljedeći slot kako bi svaki klik poslao NOVI broj bez refreša
        let nextSlot = (parseInt(clicksEl.textContent, 10) || 0) + 1;

        // Utility: postavi vrijednost brojača
        function setTodayClicks(val) {
            const n = Math.max(0, Math.min(parseInt(val || 0, 10), limitPerDay));
            clicksEl.textContent = n;
            nextSlot = n + 1; // sinkroniziraj nextSlot iz stvarnog brojača
            refreshAllSlotsUI();
        }

        // Utility: optimistički +1
        function incTodayClicksOptimistically() {
            setTodayClicks((parseInt(clicksEl.textContent, 10) || 0) + 1);
        }

        // Osvježi sve bedževe i data-slot na nedovršenim gumbima
        function refreshAllSlotsUI() {
            document.querySelectorAll('[data-slot-badge]').forEach(b => {
                b.textContent = nextSlot;
                b.classList.remove('bg-success');
                b.classList.add('bg-secondary');
            });

            document.querySelectorAll('.task-btn').forEach(b => {
                if (!b.classList.contains('disabled')) {
                    b.dataset.slot = nextSlot;
                    b.classList.remove('btn-success');
                    b.classList.remove('disabled');
                    b.classList.add('btn-outline-primary');
                    b.textContent = "{{ __('Posjeti') }}";
                    b.removeAttribute('aria-disabled');
                    b.removeAttribute('tabindex');
                }
            });
        }

        // Označi kliknuti task kao gotov (ne dira ostale)
        function markTaskDone(btn) {
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success', 'disabled');
            btn.textContent = "{{ __('Odrađeno') }}";
            btn.setAttribute('aria-disabled', 'true');
            btn.setAttribute('tabindex', '-1');

            // njegov bedž postaje zelen
            const li = btn.closest('li');
            const badge = li ? li.querySelector('[data-slot-badge]') : null;
            if (badge) {
                badge.classList.remove('bg-secondary');
                badge.classList.add('bg-success');
            }
        }

        // Klik handler
        document.querySelectorAll('.task-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (btn.classList.contains('disabled')) return;

                // dodijeli slot za ovaj klik i odmah ga "rezerviraj" za sljedeći
                const slotForThisClick = nextSlot;
                const companyId = btn.dataset.company || null;
                const targetUrl = btn.getAttribute('href');

                // optimistički: privremeno onemogući gumb da se ne dupla
                btn.classList.add('disabled');

                try {
                    const res = await fetch(clickEndpoint, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrf
                        },
                        body: JSON.stringify({
                            slot: slotForThisClick,
                            target_company_id: companyId
                        })
                    });

                    const data = await res.json().catch(() => ({}));
                    if (res.ok && data && data.success) {
                        // Označi ovaj task završenim
                        markTaskDone(btn);

                        // Ažuriraj brojač (iz backend-a ako postoji, inače optimistički)
                        if (typeof data.todayClicks !== 'undefined') {
                            setTodayClicks(data.todayClicks);
                        } else {
                            incTodayClicksOptimistically();
                        }

                        // Otvori cilj u novom tabu (ako postoji)
                        if (targetUrl && targetUrl !== '#') {
                            window.open(targetUrl, '_blank', 'noopener');
                        }
                    } else {
                        // Ako je odbijeno (npr. dupli slot), vrati gumb u prvobitno stanje
                        btn.classList.remove('disabled');
                        // alert(data?.message || 'Greška pri spremanju klika.');
                    }
                } catch (err) {
                    btn.classList.remove('disabled');
                    // alert('Greška mreže.');
                }
            });
        });
    });
</script>
@endpush
