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
                <p><strong>Broj narudžbe:</strong> {{ $payment->reference ?? $order->number ?? '—' }}</p>
                <p><strong>Iznos:</strong> {{ isset($payment) ? number_format($payment->amount, 2, ',', '.') : '—' }} {{ $payment->currency ?? 'EUR' }}</p> <p><strong>Datum i vrijeme:</strong> {{ isset($payment) && $payment->created_at ? $payment->created_at->format('d.m.Y. H:i') : now()->format('d.m.Y. H:i') }}</p>
                <h4>Što sada?</h4>
                <p>Nakon kratke provjere naš tim će objaviti vaš oglas/profil. O statusu ćemo vas obavijestiti e-poštom.</p> <p>Potvrdu o uplati i račun poslali smo na vašu e-mail adresu.</p>
                <h4>Upravljanje profilom</h4>
                <p>Svoj oglas možete urediti, dodati opis i pratiti statistike na nadzornoj ploči.</p>
                <p><strong>Certified:</strong> Ako ste odabrali certificiranje, naši stručnjaci će pregledati oglas i dodijeliti oznaku nakon odobrenja. <i>Uslugu možete aktivirati i kasnije.</i></p>
                <p>Imate pitanja? Pišite nam na info@agmedia.hr.</p>


                <div class="d-flex mt-3 gap-2">

                    <a href="{{ localized_route('companies.create') }}" class="btn btn-lg btn-outline-dark ms-auto">
                        <i class="fi-chevron-left fs-lg me-1 ms-n2"></i> {{ __('company.back') }}
                    </a>


                </div>
            </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

