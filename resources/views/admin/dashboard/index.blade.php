@extends('admin.layouts.base-admin')

@section('title', 'Nadzorna ploča')

@section('content')
    <div class="row g-2">
        {{-- GORNJE KARTICE --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted small">Tvrtke</div>
                    <div class="h4 mb-0">{{ number_format($companiesTotal) }}</div>
                    <div class="text-success small mt-1">
                        Aktivne: {{ number_format($companiesActive) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted small">Aktivne pretplate</div>
                    <div class="h4 mb-0">{{ number_format($subsActive) }}</div>
                    <div class="text-muted small mt-1">Obnove u idućih 7 dana: {{ number_format($upcomingRenewals) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted small">MRR (približno)</div>
                    <div class="h4 mb-0">{{ number_format($mrr, 2) }} EUR</div>
                    <div class="text-muted small mt-1">Mjesečna stopa prihoda</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted small">Prihod (ovaj mjesec)</div>
                    <div class="h4 mb-0">{{ number_format($revenueThisMonth, 2) }} EUR</div>
                    <div class="text-muted small mt-1">Plaćeni računi</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted small">Nepodmirena plaćanja</div>
                    <div class="h4 mb-0">{{ number_format($paymentsPending) }}</div>
                    <div class="text-warning small mt-1">Potrebna akcija</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        @php $isDown = app()->isDownForMaintenance(); @endphp
                        <div class="text-muted small">Sustav</div>
                        <div class="h4 mb-0">{{ $isDown ? 'Održavanje' : 'Online' }}</div>
                        <div class="text-warning small mt-1">{{ $isDown ? 'Stranica je nedostupna' : 'Stranica je Online' }}</div>
                    </div>

                    @if(!$isDown)
                        <form action="{{ route('tools.cache.clear') }}" method="POST" class="ms-auto">@csrf
                            <button class="btn btn-sm btn-outline-secondary">Očisti cache</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- GRAFIČKI PRIKAZI --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Plaćanja (14 dana)</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentsChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Pretplate po statusu</h6></div>
                <div class="card-body">
                    <canvas id="subsDonut" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- TABLICA NAJNOVIJIH PLAĆANJA --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Najnovija plaćanja</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:72px;">#</th>
                                <th>Tvrtka</th>
                                <th>Status</th>
                                <th>Iznos</th>
                                <th>Razdoblje</th>
                                <th>Izdano</th>
                                <th>Plaćeno</th>
                                <th>Pružatelj usluge</th>
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
                                <tr><td colspan="8">Još nema plaćanja.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card.h-100 {
            height: 100%;
        }

        /* Oba grafa imaju maksimalnu visinu */
        #paymentsChart,
        #subsDonut {
            max-height: 220px;
        }
    </style>
@endpush

@push('scripts')

    {{-- Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Linijski graf: broj plaćanja + prihod
            const ctx = document.getElementById('paymentsChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Broj plaćanja',
                            data: @json($paymentsCount),
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 0,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Prihod (plaćeno, EUR)',
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
                        y:  { beginAtZero: true, title: { display: true, text: 'Plaćanja' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'EUR' }, grid: { drawOnChartArea: false } }
                    }
                }
            });

            // Kružni graf: statusi pretplata
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
