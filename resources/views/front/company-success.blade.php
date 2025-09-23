@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    @include('components.layouts.app..checkout-steps-nav')


<div class="container-xxl py-4">

  <div class="row justify-content-start">
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h1 class="h2 mb-3">{{ __('company.success') }}</h1>
            <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.success') }}" class="vstack gap-3">
                @csrf
                <!-- Dark table with striped columns -->

                <p>Vaša uplata je uspješno zaprimljena i vaš profil tvrtke je kreiran.</p>
                <h4>Detalji uplate</h4>

                <div class="table-responsive">
                    <table class="table table-striped-columns">

                        <tbody>
                        <tr>
                            <th class="w-25 text-nowrap">Broj narudžbe: </th>
                            <td>{{ $payment->reference ?? $order->number ?? '—' }}</td>

                        </tr>
                        <tr>
                            <th class="w-25 text-nowrap">Iznos: </th>
                            <td> {{ isset($payment) ? number_format($payment->amount, 2, ',', '.') : '—' }} {{ $payment->currency ?? 'EUR' }}</td>

                        </tr>
                        <tr>
                            <th class="w-25 text-nowrap">Datum i vrijeme: </th>
                            <td>{{ isset($payment) && $payment->created_at ? $payment->created_at->format('d.m.Y. H:i') : now()->format('d.m.Y. H:i') }}</td>

                        </tr>


                        </tbody>
                    </table>
                </div>



                <h4>Što sada?</h4>
                <p>Nakon kratke provjere naš tim će objaviti vaš oglas/profil. O statusu ćemo vas obavijestiti e-poštom.</p> <p>Potvrdu o uplati i račun poslali smo na vašu e-mail adresu.</p>
                <h4>Upravljanje profilom</h4>
                <p>Svoj oglas možete urediti, dodati opis i pratiti statistike na nadzornoj ploči.</p>

                <p>Imate pitanja? Pišite nam na info@agmedia.hr.</p>


                <div class="d-flex mt-3 gap-2">

                    <a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline-dark ">
                         {{ __('Nadzorna ploča') }}
                    </a>


                </div>
            </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

