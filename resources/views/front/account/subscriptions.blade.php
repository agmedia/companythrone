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
                                    <td>{{ $sub->ends_on?->format('d.m.Y') ?? '—' }}</td>
                                    <td>{{ $sub->is_auto_renew ? __('Da') : __('Ne') }}</td>
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
