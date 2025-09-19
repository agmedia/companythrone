@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    @include('components.layouts.app..checkout-steps-nav')


<div class="container-xxl py-4">

  <div class="row justify-content-start">
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h1 class="h2 mb-3">{{ __('company.payment') }}</h1>
          <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.store') }}" class="vstack gap-3">
            @csrf

            <div class="d-flex  mt-3 gap-2">
              <button type="submit" class="btn btn-lg btn-primary">{{ __('company.submit') }}  <i class="fi-chevron-right fs-lg ms-1 me-n2"></i></button>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

