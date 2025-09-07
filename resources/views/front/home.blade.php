@extends('layouts.app')
@section('title', __('home.title'))
@section('content')
  <div class="container-xxl py-4">
    <div class="row g-4 align-items-stretch mb-4">
      <div class="col-lg-7">
        <div class="h-100 p-4 p-md-5 bg-white border rounded-3 shadow-sm">
          <h1 class="display-6 fw-bold mb-0">{{ __('home.headline') }}</h1>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="h-100 p-3 p-md-4 bg-white border rounded-3 shadow-sm">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="{{ __('search_placeholder') }}" onfocus="this.blur();" aria-label="Search" />
          </div>
          <div class="mt-2">
            <livewire:front.company-search />
          </div>
        </div>
      </div>
    </div>
    <h2 class="h6 text-uppercase text-muted mb-2">{{ __('home.featured') }}</h2>
    <div class="row g-3 mb-4">
      @forelse($featured as $c)
        <div class="col-12 col-md-6 col-xl-4">
          <a class="card border-0 shadow-sm text-decoration-none h-100" href="{{ localized_route('companies.show', $c) }}">
            <div class="card-body">
              <h3 class="h6 fw-semibold mb-1 text-dark">{{ $c->name }}</h3>
              <div class="text-muted small">
                @if($c->city) {{ $c->city }}@endif
                @if($c->state), {{ $c->state }}@endif
              </div>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-secondary mb-0">—</div>
        </div>
      @endforelse
    </div>
    @if(isset($cats) && $cats->count())
      <div class="row g-3">
        @foreach($cats as $cat)
          <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <a href="{{ localized_route('categories.show', $cat) }}" class="h6 mb-0 text-decoration-none">
                    {{ $cat->name }}
                  </a>
                  <span class="badge text-bg-light">{{ $cat->descendants()->count() }}</span>
                </div>
                @php $children = $cat->children->take(6); @endphp
                @if($children->count())
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($children as $child)
                      <a class="badge rounded-pill text-bg-secondary text-decoration-none"
                         href="{{ localized_route('categories.show', $child) }}">{{ $child->name }}</a>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection
