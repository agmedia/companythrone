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
                    {{--<div class="col-12 text-muted small">
                        {{ __('Danas:') }} {{ $todayLinks }} / {{ $limitPerDay }}
                    </div>--}}
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

                    <ul class="list-group mb-3">
                        @php
                            // dekodiraj slots samo ako postoji session i payload
                            $slots = [];
                            if (!empty($usedSlots->toArray())) {
                                $slots = $usedSlots->toArray() ?: [];
                            }
                        @endphp

                        @forelse($targets as $i => $target)
                            @php
                                $slot = $todayClicks + 1;
                                $done = in_array($slot, $slots, true);
                            @endphp

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge me-2 {{ $done ? 'bg-success' : 'bg-secondary' }}">{{ $slot }}</span>
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
            document.querySelectorAll('.task-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    let slot = btn.dataset.slot;
                    let companyId = btn.dataset.company;

                    // AJAX call da zabilježi klik
                    fetch("{{ localized_route('account.links.click') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            slot: slot,
                            target_company_id: companyId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            btn.classList.remove('btn-outline-primary');
                            btn.classList.add('btn-success','disabled');
                            btn.textContent = "{{ __('Odrađeno') }}";
                        }
                    });
                });
            });
        });
    </script>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const clicksEl = document.getElementById('today-clicks');
            const limitPerDay = {{ (int) $limitPerDay }};

            document.querySelectorAll('.task-btn').forEach(btn => {
            btn.addEventListener('click', e => {
            // prevent navigation so the fetch can complete
            e.preventDefault();

            if (btn.classList.contains('disabled')) return;

            const slot = btn.dataset.slot;
            const companyId = btn.dataset.company;
            const targetUrl = btn.getAttribute('href');

            fetch("{{ localized_route('account.links.click') }}", {
            method: "POST",
            headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
            body: JSON.stringify({
            slot: slot,
            target_company_id: companyId
        })
        })
            .then(r => r.json())
            .then(data => {
            if (data && data.success) {
            // Mark the button as done
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success','disabled');
            btn.textContent = "{{ __('Odrađeno') }}";

            // Optimistically bump the "Imate: X / Y" counter
            const current = parseInt(clicksEl.textContent, 10) || 0;
            const next = Math.min(current + 1, limitPerDay);
            clicksEl.textContent = next;

            // (Optional) if you want to open the link after recording the click:
            if (targetUrl && targetUrl !== '#') {
            window.open(targetUrl, '_blank');
        }
        }
        })
            .catch(() => {
            // You might want to show a toast or alert here on failure.
        });
        });
        });
        });
    </script>



@endpush
