@extends('admin.layouts.base-admin')

@section('title', 'Uredi pretplatu #'.$subscription->id)

@section('content')
    <div class="container">
        <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" class="card">
            @csrf @method('PUT')

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Uredi pretplatu #{{ $subscription->id }}</h5>
                <a href="{{ route('subscriptions.show', $subscription) }}" class="btn btn-light">Povratak</a>
            </div>

            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">Plan</label>
                    <input type="text" name="plan" class="form-control @error('plan') is-invalid @enderror"
                           value="{{ old('plan', $subscription->plan) }}">
                    @error('plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Razdoblje</label>
                    <select name="period" class="form-select @error('period') is-invalid @enderror">
                        @foreach(['monthly','yearly'] as $p)
                            <option value="{{ $p }}" @selected(old('period', $subscription->period) === $p)">
                            {{ $p === 'monthly' ? 'Mjesečno' : 'Godišnje' }}
                            </option>
                        @endforeach
                    </select>
                    @error('period') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Cijena</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $subscription->price) }}">
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Valuta</label>
                    <input type="text" name="currency" maxlength="3" class="form-control @error('currency') is-invalid @enderror"
                           value="{{ old('currency', $subscription->currency) }}">
                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    @php $statuses = ['trialing','active','paused','canceled','expired']; @endphp
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" @selected(old('status', $subscription->status) === $st)">
                            @switch($st)
                                @case('trialing') Probno razdoblje @break
                                @case('active') Aktivna @break
                                @case('paused') Pauzirana @break
                                @case('canceled') Otkazana @break
                                @case('expired') Istekla @break
                                @endswitch
                                </option>
                                @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_auto_renew" value="0">
                        <input class="form-check-input" type="checkbox" id="is_auto_renew" name="is_auto_renew" value="1"
                            @checked(old('is_auto_renew', $subscription->is_auto_renew))>
                        <label class="form-check-label" for="is_auto_renew">Automatska obnova</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Početak</label>
                    <input type="date" name="starts_on" class="form-control @error('starts_on') is-invalid @enderror"
                           value="{{ old('starts_on', optional($subscription->starts_on)->format('Y-m-d')) }}">
                    @error('starts_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kraj probnog razdoblja</label>
                    <input type="date" name="trial_ends_on" class="form-control @error('trial_ends_on') is-invalid @enderror"
                           value="{{ old('trial_ends_on', optional($subscription->trial_ends_on)->format('Y-m-d')) }}">
                    @error('trial_ends_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sljedeća obnova</label>
                    <input type="date" name="next_renewal_on" class="form-control @error('next_renewal_on') is-invalid @enderror"
                           value="{{ old('next_renewal_on', optional($subscription->next_renewal_on)->format('Y-m-d')) }}">
                    @error('next_renewal_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Završetak</label>
                    <input type="date" name="ends_on" class="form-control @error('ends_on') is-invalid @enderror"
                           value="{{ old('ends_on', optional($subscription->ends_on)->format('Y-m-d')) }}">
                    @error('ends_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Bilješke</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $subscription->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary">Ažuriraj</button>
                <a href="{{ route('subscriptions.show', $subscription) }}" class="btn btn-secondary">Odustani</a>
            </div>
        </form>
    </div>
@endsection
