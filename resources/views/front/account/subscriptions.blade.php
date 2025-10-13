@extends('layouts.app')

@section('title', __('Pretplate i računi'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <h1 class="h4 mb-4 mt-1">{{ __('Moje pretplate') }}</h1>

                @if($subs->isEmpty())
                    <div class="alert alert-info">{{ __('Nemate aktivnih pretplata.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle fs-sm">
                            <thead>
                            <tr>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Cijena') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Početak') }}</th>
                                <th>{{ __('Ističe') }}</th>
                                <th>{{ __('Auto renew') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subs as $sub)
                                <tr>
                                    <td>{{ ucfirst($sub->plan) }}</td>
                                    <td>{{ $sub->period === 'monthly' ? __('Mjesečno') : __('Godišnje') }}</td>
                                    <td>{{ number_format($sub->price,2,',','.') }} {{ $sub->currency }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $sub->status==='active' ? 'success' : ($sub->status==='trialing' ? 'info' : 'secondary') }}">
                                          {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $sub->starts_on?->format('d.m.Y') ?? '—' }}</td>
                                    <td class="text-muted">{{ $sub->ends_on?->format('d.m.Y') ?? $sub->starts_on->addYear()->format('d.m.Y') }}</td>
                                    <td>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input auto-renew-toggle"
                                                   type="checkbox"
                                                   role="switch"
                                                   data-id="{{ $sub->id }}"
                                                    @checked($sub->is_auto_renew)>
                                            <label class="form-check-label small text-muted">
                                                {{ $sub->is_auto_renew ? __('Da') : __('Ne') }}
                                            </label>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.auto-renew-toggle').forEach(toggle => {
                toggle.addEventListener('change', e => {
                    const subId = toggle.dataset.id;
                    const label = toggle.closest('td').querySelector('.form-check-label');
                    const state = toggle.checked;

                    fetch(`/moj-racun/subscriptions/${subId}/toggle-renew`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            label.textContent = data.label;
                            toggle.checked = data.is_auto_renew;
                        } else {
                            toggle.checked = !state;
                            alert('Greška pri ažuriranju.');
                        }
                    })
                    .catch(() => {
                        toggle.checked = !state;
                        alert('Greška pri komunikaciji.');
                    });
                });
            });
        });
    </script>
@endpush
