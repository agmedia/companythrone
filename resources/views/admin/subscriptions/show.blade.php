@extends('admin.layouts.base-admin')

@section('title', 'Pretplata #'.$subscription->id)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Pretplata #{{ $subscription->id }}</h1>
            <div class="d-flex gap-2">
                <a class="btn btn-light" href="{{ route('subscriptions.index') }}">Povratak</a>
                <a class="btn btn-primary" href="{{ route('subscriptions.edit', $subscription) }}">Uredi</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <div class="text-muted">Tvrtka</div>
                    <div>{{ $subscription->company?->email ?? '—' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Plan</div>
                    <div>{{ $subscription->plan }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Razdoblje</div>
                    <div>
                        @if($subscription->period === 'monthly') Mjesečno
                        @elseif($subscription->period === 'yearly') Godišnje
                        @else {{ ucfirst($subscription->period) }}
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Cijena</div>
                    <div>{{ number_format($subscription->price, 2) }} {{ $subscription->currency }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Status</div>
                    <span class="badge bg-{{ in_array($subscription->status,['active','trialing']) ? 'success':'secondary' }}">
                        @switch($subscription->status)
                            @case('trialing') Probno razdoblje @break
                            @case('active') Aktivna @break
                            @case('paused') Pauzirana @break
                            @case('canceled') Otkazana @break
                            @case('expired') Istekla @break
                            @default {{ ucfirst($subscription->status) }}
                        @endswitch
                    </span>
                </div>

                <div class="col-md-3">
                    <div class="text-muted">Automatska obnova</div>
                    <div>{{ $subscription->is_auto_renew ? 'Da' : 'Ne' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Kraj probnog razdoblja</div>
                    <div>{{ $subscription->trial_ends_on?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Sljedeća obnova</div>
                    <div>{{ $subscription->next_renewal_on?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Završetak</div>
                    <div>{{ $subscription->ends_on?->format('Y-m-d') ?? '—' }}</div>
                </div>

                @if($subscription->notes)
                    <div class="col-12">
                        <div class="text-muted">Bilješke</div>
                        <div class="border rounded p-2 bg-light">{{ $subscription->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Uplate za ovu pretplatu --}}
        <div class="card">
            <div class="card-header"><strong>Uplate</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Iznos</th>
                        <th>Status</th>
                        <th>Razdoblje</th>
                        <th>Izdano</th>
                        <th>Plaćeno</th>
                        <th>Pružatelj usluge</th>
                        <th>Račun</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($subscription->payments as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                            <td>
                                <form method="post" action="{{ route('payments.updateStatus', $p) }}" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PUT')

                                    <select name="status" class="form-select form-select-sm w-auto"
                                            onchange="this.form.submit()">
                                        @foreach($statuses as $s)
                                            @php
                                                $label = $s['title'][app()->getLocale()] ?? $s['title']['hr'] ?? ucfirst($s['id']);
                                            @endphp
                                            <option value="{{ $s['id'] }}" @selected($p->status == $s['id'])>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @php
                                        $current = $statuses->firstWhere('id', $p->status);
                                    @endphp
                                    <span class="badge bg-{{ $current['color'] ?? 'secondary' }}">
                                        {{ $current['title'][app()->getLocale()] ?? ucfirst($p->status) }}
                                    </span>
                                    <noscript><button class="btn btn-sm btn-primary">Spremi</button></noscript>
                                </form>
                            </td>
                            <td class="text-nowrap">
                                {{ $p->period_start?->format('Y-m-d') ?? '—' }} — {{ $p->period_end?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td>{{ $p->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $p->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="text-break">
                                {{ $p->provider }}
                                {{--@if($p->provider_ref)
                                    <small class="text-muted">({{ $p->provider_ref }})</small>
                                @endif--}}
                            </td>
                            <td>{{ $p->invoice_no ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Nema uplata.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
