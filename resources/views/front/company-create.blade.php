@extends('layouts.app')
@section('title', __('company.add'))
@section('content')
<div class="container-xxl py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ __('company.add') }}</li>
    </ol>
  </nav>
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h1 class="h5 mb-3">{{ __('company.add') }}</h1>
          <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.store') }}" class="vstack gap-3">
            @csrf
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label" for="name">{{ __('company.name') }} *</label>
                <input id="name" name="name" value="{{ old('name') }}" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="oib">{{ __('company.oib') }} *</label>
                <input id="oib" name="oib" value="{{ old('oib') }}" class="form-control" required>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="email">{{ __('auth.email') }} *</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="website">{{ __('company.website') }}</label>
                <input id="website" name="website" class="form-control" placeholder="https://example.com" value="{{ old('website') }}">
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="street">{{ __('company.street') }}</label>
                <input id="street" name="street" class="form-control" value="{{ old('street') }}">
              </div>
              <div class="col-md-2">
                <label class="form-label" for="street_no">{{ __('company.street_no') }}</label>
                <input id="street_no" name="street_no" class="form-control" value="{{ old('street_no') }}">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="city">{{ __('company.city') }}</label>
                <input id="city" name="city" class="form-control" value="{{ old('city') }}">
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="state">{{ __('company.state') }}</label>
                <input id="state" name="state" class="form-control" value="{{ old('state') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="phone">{{ __('company.phone') }}</label>
                <input id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
              </div>
            </div>
            <div>
              <label class="form-label" for="logo">{{ __('company.logo') ?? 'Logo' }}</label>
              <input id="logo" type="file" name="logo" class="form-control">
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">{{ __('company.submit') }}</button>
              <a href="{{ localized_route('home') }}" class="btn btn-outline-secondary">&laquo; {{ __('nav.home') }}</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
