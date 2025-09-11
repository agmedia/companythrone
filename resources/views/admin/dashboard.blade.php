{{-- resources/views/layouts/admin.blade.php --}}

@extends('back.layouts.base-admin')

@section('title', __('back/common.dashboard'))

@section('content')
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="page-header-title">
                        <h5 class="m-b-10">@yield('title', 'Dashboard')</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ul>
                </div>
                <div class="col-md-6 text-end">
                    @yield('page-actions')
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-sm-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-0">@lang('back/common.dashboard')</h5>
                    <p class="text-muted mb-0">Admin dashboard loaded.</p>
                </div>
            </div>

        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection
