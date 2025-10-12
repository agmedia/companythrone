@extends('layouts.app')

@section('title', __('Pretplate i računi'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <h1 class="h4 mb-4 mt-1">{{ __('Moji računi') }}</h1>

                @if($invoices->isEmpty())
                    <div class="alert alert-info">{{ __('Nemate još kreiranih računa.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle fs-sm">
                            <thead>
                            <tr>
                                <th>{{ __('Broj računa') }}</th>
                                <th>{{ __('Datum') }}</th>
                                <th>{{ __('Razdoblje') }}</th>
                                <th>{{ __('Iznos') }}</th>
                                <th>{{ __('Način plaćanja') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $inv)
                                <tr>
                                    <td>{{ $inv['number'] }}</td>
                                    <td>{{ $inv['date']->format('d.m.Y') }}</td>
                                    <td>{{ $inv['period'] ?? '—' }}</td>
                                    <td>{{ number_format($inv['amount'],2,',','.') }} {{ $inv['currency'] }}</td>
                                    <td>{{ $inv['provider'] }}</td>
                                    <td>
                                        @php $status = $statuses->where('id', $inv['status'])->first() @endphp
                                        <span class="badge text-bg-{{ $status['color'] }}">
                                              {{ $status['title'][current_locale()] }}
                                            </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ( ! subscription_active($user->company->id))
                    <h1 class="h4 mb-4 mt-1">{{ __('Načini Plaćanja') }}</h1>

                    <div class="list-group mb-4">
                        @forelse($payments as $pay)
                            <div class="list-group-item">
                                <div class="fw-semibold">{{ $pay['name']->{current_locale()} ?? $pay['code'] }}</div>
                                @if (is_array($pay['short_description']))
                                    <div class="small text-muted">{{ $pay['short_description']['hr'] ?: '' }}</div>
                                @else
                                    <div class="small text-muted">{{ $pay['short_description'] ?: '' }}</div>
                                @endif
                                @if(isset($pay['price']))
                                    <div class="small">{{ __('Cijena dodatno') }}: {{ $pay['price'] }} €</div>
                                @endif

                                <div class="float-end">
                                    <a href="{{ route('companies.payment') }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('Plati') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">{{ __('Trenutno nema aktivnih metoda plaćanja.') }}</div>
                        @endforelse
                    </div>
                @endif

                {{--<a href="{{ route('account.invoices') }}" class="btn btn-outline-primary">
                    {{ __('Pregled računa / faktura') }}
                </a>--}}
            </div>
        </div>
    </div>
@endsection
