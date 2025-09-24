@extends('layouts.app')
@section('title', __('home.title'))
@section('content')

    <div class="container py-5 mt-n3 mt-sm-0 my-xxl-3">
        <div class="row pb-2 pb-sm-3 py-md-4 py-lg-5">

            <!-- Intro + Phone -->
            <div class="col-md-5 col-lg-6 mb-5 mb-md-0">
                <h2 class="h-6 mb-sm-4">Kontaktirajte nas</h2>
                <p class="pb-3 pb-md-0 mb-4 mb-md-5" style="max-width: 460px">Javite nam se ako imate problema s korištenjem usluge. Otvoreni smo za razgovor i želimo bolje upoznati one koji nam vjeruju.</p>

                <!-- Phone -->
                <div class="d-flex align-items-center">
                    <div class="position-relative flex-shrink-0 y rounded-circle rtl-flip" style="width: 90px; height: 90px;background-color:rgb(201, 45, 45)">
                        <img src="{{ asset('theme1/assets/companythrone-round.svg') }}" class="d-block rounded-circle"  style="padding: 20px" alt="Companythrone"/>
                        <span class="position-absolute top-0 start-0 w-100 h-100 border border-3 border-white rounded-circle d-none-dark"></span>
                        <span class="position-absolute top-0 start-0 w-100 h-100 border border-3 rounded-circle d-none d-block-dark"></span>
                        <div class="position-absolute d-flex align-items-center justify-content-center z-2" style="right: 0; bottom: 0; width: 16px; height: 16px; margin: 0 9px 3px 0">
                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-circle d-none-dark"></span>
                            <span class="position-absolute top-0 start-0 w-100 h-100 border border-3 rounded-circle d-none d-block-dark"></span>
                            <span class="position-relative z-1 bg-info rounded-circle" style="width: 12px; height: 12px"></span>
                        </div>

                    </div>
                    <div class="ps-3 ps-sm-4">
                        <h5 class="mb-2">Imate pitanja?</h5>
                        <div class="nav gap-2 fs-sm">
                            <span>Nazovi nas odmah</span>
                            <a class="nav-link hover-effect-underline fw-semibold p-0" href="tel:+385 1 23 83 733">+385 1 23 83 733</a>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Contact form -->
            <div class="col-md-7 col-lg-6">
                <form action="{{ route('poruka') }}" method="POST" class="needs-validation position-relative bg-body rounded-5" style="padding: 5px" novalidate>
                    <div class="position-relative z-1 my-2 mx-sm-2 m-lg-0 p-4 p-lg-5">
                        <h2 class="h4 mb-4">Kontakt obrazac!</h2>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" placeholder="Ime i prezime *" required>
                            <div class="invalid-feedback">Molimo unesite svoje ime i prezime!</div>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg text-start" placeholder="Email *" required>
                            <div class="invalid-feedback">Molimo unesite važeću email adresu!</div>
                        </div>
                        <div class="mb-4">
                            <textarea class="form-control form-control-lg" rows="5" placeholder="Vaša poruka *" required></textarea>
                            <div class="invalid-feedback">Molimo napišite svoju poruku!</div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-info w-100">Pošalji</button>
                    </div>
                    <span class="position-absolute top-0 start-0 w-100 h-100 border border-5 rounded-5 opacity-50"></span>
                </form>

            </div>
        </div>



    </div>

@endsection
@push('js_after')
    @include('components.layouts.app.recaptcha-js')
@endpush
