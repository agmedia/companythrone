@extends('admin.layouts.base-admin')

@section('title', 'Uplate')

@section('content')
    <div class="card">
        {{-- 🔍 Filter header --}}
        <div class="card-header align-items-center justify-content-between d-flex">
            <h5 class="mb-1">Uplate</h5>

            <form method="GET" action="{{ route('payments.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Sve</option>
                        @foreach($statuses as $s)
                            @php
                                $label = $s['title'][app()->getLocale()] ?? $s['title']['hr'] ?? ucfirst($s['id']);
                            @endphp
                            <option value="{{ $s['id'] }}" @selected(request('status')==$s['id'])>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label small mb-1">Tvrtka</label>
                    <input type="text" name="company" class="form-control form-control-sm" value="{{ request('company') }}" placeholder="naziv tvrtke">
                </div>

                <div class="col-auto">
                    <label class="form-label small mb-1">Pružatelj usluge</label>
                    <input type="text" name="provider" class="form-control form-control-sm" value="{{ request('provider') }}" placeholder="npr. Bank, Stripe...">
                </div>

                <div class="col-auto">
                    <label class="form-label small mb-1">Plaćeno od</label>
                    <input type="date" name="paid_from" class="form-control form-control-sm" value="{{ request('paid_from') }}">
                </div>

                <div class="col-auto">
                    <label class="form-label small mb-1">do</label>
                    <input type="date" name="paid_to" class="form-control form-control-sm" value="{{ request('paid_to') }}">
                </div>

                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary">Filtriraj</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-light">Poništi</a>
                </div>
            </form>
        </div>

        {{-- 📋 Tablica uplata --}}
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tvrtka</th>
                    <th>Iznos</th>
                    <th>Status</th>
                    <th>Razdoblje</th>
                    <th>Plaćeno</th>
                    <th>Račun</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $p)
                    @php
                        $statusData = collect($statuses)->firstWhere('id', $p->status);
                        $color = $statusData['color'] ?? 'secondary';
                        $title = $statusData['title'][app()->getLocale()] ?? ucfirst($p->status);
                    @endphp
                    <tr data-id="{{ $p->id }}">
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->company->t_name ?? '—' }}</td>
                        <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                        <td style="min-width: 180px">
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm payment-status" data-id="{{ $p->id }}">
                                    @foreach($statuses as $s)
                                        @php $label = $s['title'][app()->getLocale()] ?? $s['title']['hr']; @endphp
                                        <option value="{{ $s['id'] }}" @selected($p->status == $s['id'])>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="badge bg-{{ $color }} status-badge">{{ $title }}</span>
                            </div>
                        </td>
                        <td>{{ $p->period_start?->format('Y-m-d') }} — {{ $p->period_end?->format('Y-m-d') }}</td>
                        <td>{{ $p->paid_at?->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $p->invoice_no ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('payments.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Uredi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Nema uplata.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>

    {{-- 🔁 Inline promjena statusa --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.payment-status').forEach(select => {
                    select.addEventListener('change', e => {
                        const paymentId = select.dataset.id;
                        const newStatus = select.value;
                        const row = select.closest('tr');
                        const badge = row.querySelector('.status-badge');

                        fetch(`/admin/payments/${paymentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(r => r.json().catch(() => null))
                        .then(data => {
                            if (data?.success) {
                                badge.className = 'badge bg-' + (data.color ?? 'secondary') + ' status-badge';
                                badge.textContent = data.label ?? newStatus;
                            } else {
                                badge.className = 'badge bg-danger status-badge';
                                badge.textContent = 'Greška';
                            }
                        })
                        .catch(() => {
                            badge.className = 'badge bg-danger status-badge';
                            badge.textContent = 'Greška';
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
