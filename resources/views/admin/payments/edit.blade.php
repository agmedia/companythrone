@extends('admin.layouts.base-admin')

@section('title', 'Uredi uplatu')

@section('content')
    @php
        // Dohvati aktivne pružatelje usluga
        $activeProviders = (new \App\Services\Settings\SettingsManager())->paymentsActive();
    @endphp

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Uredi uplatu #{{ $payment->id }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('payments.update', $payment) }}" class="vstack gap-3">
                @csrf


                <div class="row g-3">
                    {{-- STATUS --}}
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach($statuses as $s)
                                @php
                                    $label = $s['title'][app()->getLocale()] ?? $s['title']['hr'] ?? ucfirst($s['id']);
                                @endphp
                                <option value="{{ $s['id'] }}" @selected($payment->status == $s['id'])>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- INVOICE --}}
                    <div class="col-md-4">
                        <label class="form-label">Račun (invoice_no)</label>
                        <input type="text" name="invoice_no" value="{{ old('invoice_no', $payment->invoice_no) }}" class="form-control">
                    </div>

                    {{-- PROVIDER --}}
                    <div class="col-md-4">
                        <label class="form-label">Pružatelj usluge</label>
                        <select name="provider" class="form-select">
                            @foreach($activeProviders as $provider)
                                <option value="{{ $provider['driver'] }}" @selected($payment->driver === $provider['driver'])>
                                    {{ $provider['label'] ?? ucfirst($provider['name']->{current_locale()}) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DATUM PLAĆANJA --}}
                    <div class="col-md-4">
                        <label class="form-label">Datum plaćanja</label>
                        <input type="date"
                               name="paid_at"
                               value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d')) }}"
                               class="form-control">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Spremi promjene</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Natrag</a>
                </div>
            </form>
        </div>
    </div>
@endsection
