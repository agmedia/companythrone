@extends('layouts.app')

@section('title', __('Moji računi'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <div class="container py-4">
                    <h1 class="h4 mb-4">{{ __('Moji računi') }}</h1>

                    @if($userInvoices->isEmpty())
                        <div class="alert alert-info">{{ __('Nemate još kreiranih računa.') }}</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
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
                                @foreach($userInvoices as $inv)
                                    <tr>
                                        <td>{{ $inv['number'] }}</td>
                                        <td>{{ $inv['date']->format('d.m.Y') }}</td>
                                        <td>{{ $inv['period'] ?? '—' }}</td>
                                        <td>{{ number_format($inv['amount'],2,',','.') }} {{ $inv['currency'] }}</td>
                                        <td>{{ $inv['provider'] }} ({{ $inv['method'] }})</td>
                                        <td>
                                            <span class="badge text-bg-{{ $inv['status']==='paid' ? 'success' : ($inv['status']==='pending' ? 'warning' : 'danger') }}">
                                              {{ ucfirst($inv['status']) }}
                                            </span>
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
    </div>

@endsection
