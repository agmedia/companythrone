@extends('layouts.app')
@section('title', $category->name)

@section('content')

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
            </li>
            @foreach($breadcrumbs as $crumb)
                @if($crumb->is($category))
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb->name }}</li>
                @else
                    <li class="breadcrumb-item">
                        {{-- izmjena: koristi nav() helper --}}
                        <a href="{{ nav()->urlById($crumb->id) }}">{{ $crumb->name }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ $category->name }}</h1>
        <span class="badge text-bg-primary">
            {{ trans_choice('category.company_count', $companies->total(), ['count'=>$companies->total()]) }}
        </span>
    </div>

    {{-- Subcategories --}}
    @if($children->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">{{ __('category.subcategories') }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($children as $child)
                        <a class="badge text-bg-light text-decoration-none"
                           href="{{ nav()->urlById($child->id) }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Companies list --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
        @forelse($companies as $c)
            <div class="col">
                <a href="{{ localized_route('companies.show', ['company' => $c]) }}"
                   class="card h-100 text-decoration-none text-reset shadow-sm border-0">
                    @if($c->hasMedia('logo'))
                        <img src="{{ $c->getFirstMediaUrl('logo') }}"
                             class="card-img-top object-fit-contain p-3" alt="{{ $c->name }}" style="height:140px;">
                    @endif
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-1">{{ $c->name }}</h3>
                        <div class="text-muted small">{{ $c->city }}, {{ $c->state }}</div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col">
                <div class="alert alert-secondary w-100 mb-0">{{ __('category.no_companies') }}</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $companies->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
@endsection
