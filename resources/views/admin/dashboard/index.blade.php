@extends('admin.layouts.base-admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-3">
        {{-- TOP CARDS --}}
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Companies</div>
                    <div class="h4 mb-0">{{ number_format($companiesTotal) }}</div>
                    <div class="text-success small mt-1">
                        Active: {{ number_format($companiesActive) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Active subscriptions</div>
                    <div class="h4 mb-0">{{ number_format($subsActive) }}</div>
                    <div class="text-muted small mt-1">Next 7d renewals: {{ number_format($upcomingRenewals) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">MRR (approx.)</div>
                    <div class="h4 mb-0">{{ number_format($mrr, 2) }} EUR</div>
                    <div class="text-muted small mt-1">Monthly run-rate</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Revenue (this month)</div>
                    <div class="h4 mb-0">{{ number_format($revenueThisMonth, 2) }} EUR</div>
                    <div class="text-muted small mt-1">Paid invoices</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Pending payments</div>
                    <div class="h4 mb-0">{{ number_format($paymentsPending) }}</div>
                    <div class="text-warning small mt-1">Action required</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    @php $isDown = app()->isDownForMaintenance(); @endphp
                    <div class="text-muted small">System</div>
                    <div class="h4 mb-0">{{ $isDown ? 'Maintenance' : 'Online' }}</div>
                    <div class="d-flex gap-2 mt-2">
                        @if(!$isDown)
                            <form action="{{ route('tools.cache.clear') }}" method="POST">@csrf
                                <button class="btn btn-sm btn-outline-secondary">Clear cache</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS --}}
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Payments (14 days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentsChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Subscriptions by status</h6></div>
                <div class="card-body">
                    <canvas id="subsDonut" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- LATEST PAYMENTS TABLE --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Latest payments</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:72px;">#</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Period</th>
                                <th>Issued</th>
                                <th>Paid</th>
                                <th>Provider</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($latestPayments as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td class="text-break">{{ $p->company?->email ?? '—' }}</td>
                                    <td>
                                    <span class="badge
                                        @if($p->status === 'paid') bg-success
                                        @elseif($p->status === 'pending') bg-warning
                                        @elseif($p->status === 'failed') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                    </td>
                                    <td class="text-nowrap">{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                                    <td class="text-nowrap">
                                        {{ $p->period_start?->format('Y-m-d') ?? '—' }} — {{ $p->period_end?->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td class="text-nowrap">{{ $p->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="text-nowrap">{{ $p->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="text-break">{{ $p->provider ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8">No payments yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-pjYf+JH5Oqj8m1H4LT1mTzqQG6G9x6vD0Cw8J0Q6JzZ9uQpG4t5xQ8k8v5jN8YQJ" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Line chart: payments count + revenue
            const ctx = document.getElementById('paymentsChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Payments count',
                            data: @json($paymentsCount),
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 0,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue (paid, EUR)',
                            data: @json($paymentsSum),
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 0,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y:  { beginAtZero: true, title: { display: true, text: 'Payments' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'EUR' }, grid: { drawOnChartArea: false } }
                    }
                }
            });

            // Donut: subscription statuses
            const ctx2 = document.getElementById('subsDonut');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: @json($subStatusLabels),
                    datasets: [{ data: @json($subStatusData) }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        });
    </script>
@endpush
