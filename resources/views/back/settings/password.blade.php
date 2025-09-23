@extends('layouts.app')
@section('title', __('settings.password_title'))

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

            <div class="col-lg-9">

                 <livewire:settings.password />

            </div>
        </div>
    </div>
@endsection
