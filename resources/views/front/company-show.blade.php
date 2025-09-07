@extends('layouts.app')
@section('title', $company->name)
@section('content')
<div class="container-xxl py-4">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center gap-3 mb-3">
            @if($company->hasMedia('logo'))
              <img src="{{ $company->getFirstMediaUrl('logo') }}" alt="{{ $company->name }}" class="rounded" style="width:64px;height:64px;object-fit:contain;">
            @else
              <div class="rounded bg-light d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                <i class="bi bi-building fs-3 text-muted"></i>
              </div>
            @endif
            <div>
              <h1 class="h4 mb-1">{{ $company->name }}</h1>
              <div class="text-muted small">
                @if($company->city) {{ $company->city }}@endif
                @if($company->state), {{ $company->state }}@endif
              </div>
            </div>
          </div>
          <div class="vstack gap-1 small text-muted mb-3">
            @if($company->street)
              <div><i class="bi bi-geo-alt me-1"></i>{{ $company->street }} {{ $company->street_no }}{{ $company->city ? ', '.$company->city : '' }}</div>
            @endif
            <div><i class="bi bi-envelope me-1"></i>{{ $company->email }}</div>
            @if($company->phone)
              <div><i class="bi bi-telephone me-1"></i>{{ $company->phone }}</div>
            @endif
          </div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach($company->categories as $cat)
              <a href="{{ localized_route('categories.show', $cat) }}" class="badge rounded-pill text-bg-secondary text-decoration-none">{{ $cat->name }}</a>
            @endforeach
          </div>
          @if($company->is_link_active)
            <a href="{{ $company->website ?? '#' }}" class="btn btn-success" target="_blank" rel="noopener">
              <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('company.visit_site') }}
            </a>
          @else
            <div class="alert alert-warning mb-0">
              {{ __('company.link_inactive') }}
            </div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      @if (class_exists(\App\Livewire\Front\DailyButtons::class))
        <livewire:front.daily-buttons :company="$company" />
      @endif
    </div>
  </div>
</div>
@endsection
