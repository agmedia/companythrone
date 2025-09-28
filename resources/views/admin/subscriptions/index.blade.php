@extends('admin.layouts.base-admin')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">

                <div class="card-header align-items-center justify-content-between d-flex">
                    <h5 class="mb-1">{{ __('back/subscriptions.title') }}</h5>

                    {{-- Filteri --}}
                    <form method="GET" action="{{ route('subscriptions.index') }}" class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                @php $opts = ['all'=>'Sve','trialing'=>'Probno','active'=>'Aktivna','paused'=>'Pauzirana','canceled'=>'Otkazana','expired'=>'Istekla']; @endphp
                                @foreach($opts as $val => $label)
                                    <option value="{{ $val }}" @selected(request('status','all')===$val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--<div class="col-auto">
                            <label class="form-label small mb-1">Razdoblje</label>
                            <select name="period" class="form-select form-select-sm">
                                <option value="">Sve</option>
                                <option value="monthly" @selected(request('period')==='monthly')>Mjesečno</option>
                                <option value="yearly"  @selected(request('period')==='yearly')>Godišnje</option>
                            </select>
                        </div>--}}
                        <div class="col-auto">
                            <label class="form-label small mb-1">Automatska obnova</label>
                            <select name="auto" class="form-select form-select-sm">
                                <option value="">Sve</option>
                                <option value="1" @selected(request('auto')==='1')>Da</option>
                                <option value="0" @selected(request('auto')==='0')>Ne</option>
                            </select>
                        </div>
                        {{-- <div class="col-auto">
                            <label class="form-label small mb-1">Paket</label>
                            <input type="text" name="plan" class="form-control form-control-sm" value="{{ request('plan') }}" placeholder="npr. pro">
                        </div>--}}
                        <div class="col-auto">
                            <label class="form-label small mb-1">E-pošta tvrtke</label>
                            <input type="text" name="email" class="form-control form-control-sm" value="{{ request('email') }}" placeholder="email@domena">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-1">Obnova od</label>
                            <input type="date" name="renew_from" class="form-control form-control-sm" value="{{ request('renew_from') }}">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-1">do</label>
                            <input type="date" name="renew_to" class="form-control form-control-sm" value="{{ request('renew_to') }}">
                        </div>
                        <div class="col-auto d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary">Filtriraj</button>
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-light">Poništi</a>
                        </div>
                    </form>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>
                @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th>Tvrtka</th>
                                {{-- <th>Paket</th>
                                <th>Razdoblje</th> --}}
                                <th>Cijena</th>
                                <th>Status</th>
                                <th>Auto. obnova</th>
                                <th>Obnova</th>
                                <th class="text-end" style="width:240px;">{{ __('back/companies.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $statusLabels = [
                                    'trialing' => 'Probno',
                                    'active'   => 'Aktivna',
                                    'paused'   => 'Pauzirana',
                                    'canceled' => 'Otkazana',
                                    'expired'  => 'Istekla',
                                ];
                            @endphp
                            @forelse($subscriptions as $s)
                                <tr>
                                    <td>{{ $s->id }}</td>
                                    <td class="text-break">{{ $s->company?->email ?? '—' }}</td>
                                    {{-- <td>{{ $s->plan }}</td>
                                    <td class="text-nowrap">{{ ucfirst($s->period) }}</td> --}}
                                    <td class="text-nowrap">{{ number_format($s->price, 2) }} {{ $s->currency }}</td>
                                    <td>
                                        @php
                                            $badge = in_array($s->status, ['active','trialing']) ? 'bg-success'
                                                : ($s->status === 'paused' ? 'bg-warning' : 'bg-outline-secondary');
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $statusLabels[$s->status] ?? ucfirst($s->status) }}</span>
                                    </td>
                                    <td>{{ $s->is_auto_renew ? 'Da' : 'Ne' }}</td>
                                    <td class="text-nowrap">{{ $s->next_renewal_on?->format('Y-m-d') ?? '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap gap-1">
                                            {{-- Prikaži --}}
                                            <a href="{{ route('subscriptions.show', $s) }}"
                                               class="btn btn-sm btn-outline-secondary" title="{{ __('back/common.actions.show') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>

                                            {{-- Uredi --}}
                                            <a href="{{ route('subscriptions.edit', $s) }}"
                                               class="btn btn-sm btn-outline-primary" title="{{ __('back/common.actions.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            {{-- Brze radnje (stanja) --}}
                                            @if(in_array($s->status, ['trialing','paused','canceled','expired']))
                                                <form action="{{ route('subscriptions.activate', $s) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-success" title="Aktiviraj">Aktiviraj</button>
                                                </form>
                                            @endif

                                            @if($s->status === 'active')
                                                <form action="{{ route('subscriptions.pause', $s) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-warning" title="Pauziraj">Pauziraj</button>
                                                </form>
                                            @endif

                                            @if($s->status === 'paused')
                                                <form action="{{ route('subscriptions.resume', $s) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-info" title="Nastavi">Nastavi</button>
                                                </form>
                                            @endif

                                            @if(in_array($s->status, ['trialing','active','paused']))
                                                <form action="{{ route('subscriptions.cancel', $s) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Želite li otkazati ovu pretplatu?')">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-outline-danger" title="Otkaži">Otkaži</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9">Nema pronađenih pretplata.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($subscriptions, 'links'))
                    <div class="card-footer">
                        {{ $subscriptions->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
