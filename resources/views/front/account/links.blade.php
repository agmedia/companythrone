@extends('layouts.app')
@section('title', __('Moji linkovi'))
@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <div class="container py-4">
                    <h1 class="h4 mb-3">{{ __('Moji linkovi') }}</h1>

                    <form method="post" action="{{ localized_route('account.links.store') }}" class="row g-2 mb-3">
                        @csrf
                        <div class="col-md-8">
                            <input name="url" type="url" class="form-control" placeholder="https://..." required>
                        </div>
                        <div class="col-md-3">
                            <input name="label" type="text" class="form-control" placeholder="{{ __('Opis (opcionalno)') }}">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary" @disabled($todayLinks >= $limitPerDay)>{{ __('Dodaj') }}</button>
                        </div>
                        <div class="col-12 text-muted small">
                            {{ __('Danas:') }} {{ $todayLinks }} / {{ $limitPerDay }}
                        </div>
                    </form>

                    <div class="vstack gap-2">
                        @foreach($links as $link)
                            <div class="d-flex align-items-center justify-content-between border rounded p-2">
                                <div class="text-truncate me-3">
                                    <div class="fw-semibold">{{ $link->label ?? __('Bez naziva') }}</div>
                                    <a href="{{ $link->url }}" target="_blank" class="small">{{ $link->url }}</a>
                                </div>
                                <div class="text-muted small">{{ __('Klikovi:') }} {{ $link->clicks }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">{{ $links->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>

            <div class="mt-5">
                <h5 class="fw-semibold">{{ __('Moje preporuke') }}</h5>

                <div class="small text-muted mb-2">
                    {{ __('Dodajte barem 5 preporuka da biste aktivirali svoj link.') }}
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


            <div class="mb-4">
                <h5 class="fw-semibold">{{ __('Dnevni zadaci (25 klikova)') }}</h5>
                <ul class="list-group mb-3">
                    @foreach($targets as $i => $target)
                        @php $slot = $i+1; @endphp
                        @php $done = in_array($slot, json_decode($session->slots_payload, true) ?? []); @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge me-2 {{ $done ? 'bg-success' : 'bg-secondary' }}">{{ $slot }}</span>
                                {{ $target->t_name ?? '—' }}
                            </div>
                            <a href="{{ $target->weburl }}"
                               target="_blank"
                               class="btn btn-sm {{ $done ? 'btn-success disabled' : 'btn-outline-primary task-btn' }}"
                               data-slot="{{ $slot }}"
                               data-company="{{ $target->id }}">
                                {{ $done ? __('Odrađeno') : __('Posjeti') }}
                            </a>
                        </li>

                    @endforeach
                </ul>
                <div class="small text-muted">
                    {{ __('Dovršeno:') }} {{ $session->completed_count }} / {{ $limitPerDay }}
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
@endpush
