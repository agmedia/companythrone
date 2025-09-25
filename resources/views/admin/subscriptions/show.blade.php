@extends('admin.layouts.base-admin')

@section('title', 'Subscription #'.$subscription->id)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Subscription #{{ $subscription->id }}</h1>
            <div class="d-flex gap-2">
                <a class="btn btn-light" href="{{ route('subscriptions.index') }}">Back</a>
                <a class="btn btn-primary" href="{{ route('subscriptions.edit', $subscription) }}">Edit</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <div class="text-muted">Company</div>
                    <div>{{ $subscription->company?->email ?? '—' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Plan</div>
                    <div>{{ $subscription->plan }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Period</div>
                    <div>{{ ucfirst($subscription->period) }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted">Price</div>
                    <div>{{ number_format($subscription->price, 2) }} {{ $subscription->currency }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Status</div>
                    <span class="badge bg-{{ in_array($subscription->status,['active','trialing']) ? 'success':'secondary' }}">
                    {{ ucfirst($subscription->status) }}
                </span>
                </div>

                <div class="col-md-3">
                    <div class="text-muted">Auto-renew</div>
                    <div>{{ $subscription->is_auto_renew ? 'Yes' : 'No' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Trial ends</div>
                    <div>{{ $subscription->trial_ends_on?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Next renewal</div>
                    <div>{{ $subscription->next_renewal_on?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Ends on</div>
                    <div>{{ $subscription->ends_on?->format('Y-m-d') ?? '—' }}</div>
                </div>

                @if($subscription->notes)
                    <div class="col-12">
                        <div class="text-muted">Notes</div>
                        <div class="border rounded p-2 bg-light">{{ $subscription->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payments for this subscription --}}
        <div class="card">
            <div class="card-header"><strong>Payments</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Period</th>
                        <th>Issued</th>
                        <th>Paid</th>
                        <th>Provider</th>
                        <th>Invoice</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($subscription->payments as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                            <td>{{ ucfirst($p->status) }}</td>
                            <td class="text-nowrap">
                                {{ $p->period_start?->format('Y-m-d') ?? '—' }} — {{ $p->period_end?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td>{{ $p->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $p->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="text-break">{{ $p->provider }} @if($p->provider_ref) <small class="text-muted">({{ $p->provider_ref }})</small>@endif</td>
                            <td>{{ $p->invoice_no ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No payments.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
