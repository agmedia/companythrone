@extends('layouts.app')
@section('title', $title)

@section('content')

    <div class="container py-5 mt-n3 mt-sm-0 my-xxl-3">
        {{-- Breadcrumbs (isti pattern kao gore) --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
                </li>

                @foreach($breadcrumbs as $crumb)
                    @php
                        $isActive = $crumb->is($category);
                        $t = method_exists($crumb, 'translation') ? $crumb->translation(app()->getLocale()) : null;
                        $title = $t?->name ?? $crumb->name ?? '—';
                    @endphp

                    @if($isActive)
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ nav()->urlById($crumb->id) }}">{{ $title }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <h1 class="h3 mb-3">{{ $title }}</h1>

        @if(!empty($content))
            <article class="prose">
                {!! $content !!}
            </article>
        @else
            <div class="alert alert-secondary">{{ __('common.no_content') }}</div>
        @endif
    </div>

@endsection
