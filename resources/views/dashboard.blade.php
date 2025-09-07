@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0">Dobrodošao, {{ auth()->user()->name ?? 'korisniče' }}</h1>
        <a class="btn btn-primary" href="{{ localized_route('companies.create') }}">
            <i class="bi bi-building-add me-1"></i> {{ __('nav.add_company') }}
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0"><div class="card-body">
                    <div class="text-muted small">Tvrtke</div>
                    <div class="display-6 fw-semibold">{{ \App\Models\Back\Catalog\Company::count() }}</div>
                </div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0"><div class="card-body">
                    <div class="text-muted small">Kategorije</div>
                    <div class="display-6 fw-semibold">{{ \App\Models\Back\Catalog\Category::count() }}</div>
                </div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0"><div class="card-body">
                    <div class="text-muted small">Aktivni linkovi</div>
                    <div class="display-6 fw-semibold">
                        {{ \App\Models\Back\Catalog\Company::where('is_link_active',true)->count() }}
                    </div>
                </div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0"><div class="card-body">
                    <div class="text-muted small">Današnji klikovi</div>
                    <div class="display-6 fw-semibold">
                        {{ \App\Models\Shared\Click::whereDate('created_at', today())->count() }}
                    </div>
                </div></div>
        </div>
    </div>
@endsection
