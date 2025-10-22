@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    <div class="container pt-4 pt-sm-5 pb-5 mb-xxl-3">
        <div class="row pt-2 pt-sm-0 pt-lg-2 pb-2 pb-sm-3 pb-md-4 pb-lg-5">


            <!-- Sidebar navigation that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
            <aside class="col-lg-3" style="margin-top: -105px">
                <div class="offcanvas-lg offcanvas-start sticky-lg-top pe-lg-3 pe-xl-4" id="accountSidebar">
                    <div class="d-none d-lg-block" style="height: 105px"></div>

                    <!-- Header -->
                    <div class="offcanvas-header d-lg-block py-3 p-lg-0">
                        <div class="d-flex flex-row flex-lg-column align-items-center align-items-lg-start">

                            <div class="pt-lg-3 ps-3 ps-lg-0">
                                <h6 class="mb-1">{{ auth()->user()->name  }}</h6>
                                <p class="fs-sm mb-0">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#accountSidebar" aria-label="Close"></button>
                    </div>


                    @include('components.layouts.app.usernav')

                    <!-- Body (Navigation) -->

                </div>
            </aside>


            <!-- Account profile content -->
            <div class="col-lg-9">
                <h2 class="h5 mb-1">Dobrodošao, {{ auth()->user()->name ?? 'korisniče' }}</h2>
                <p>Link vaše tvrtke postaje aktivan tek nakon što klikne svih {{ app_settings()->clicksRequired() }} linkova.</p>
                <!-- Wallet + Account progress -->
                <section class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 g-xl-4 pb-5 mb-md-3">
                    <div class="col">
                        <div class="card bg-info-subtle border-0 h-100">

                            <div class="card">
                                <!-- Wrap the image with a "ratio" element to avoid content shifts on page load. Formula: imageHeight / imageWidth * 100% -->
                                <div >
                                    <img src="{{ asset('theme1/images/default_image.jpg') }}" class="card-img-top" alt="Card image">
                                </div>
                                <div class="card-body">
                                    <a href="#" class="btn btn-primary w-100"><i class="fi-link me-2"></i> Web stranica</a>
                                </div>
                            </div>



                        </div>
                    </div>

                </section>










            </div>
        </div>
    </div>



@endsection
