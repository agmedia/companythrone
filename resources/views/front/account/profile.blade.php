@extends('layouts.app')

@section('title', __('Moj profil'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <h1 class="h4 mb-4">{{ __('Moj profil') }}</h1>

                <form method="post" action="{{ route('account.profile.update') }}" class="vstack gap-3">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Ime i prezime') }}</label>
                        <input id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('E-mail') }}</label>
                        <input id="email" type="email" name="email" class="form-control"
                               value="{{ old('email', auth()->user()->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Nova lozinka') }}</label>
                        <input id="password" type="password" name="password" class="form-control">
                        <div class="form-text">{{ __('Ostavite prazno ako ne mijenjate lozinku.') }}</div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Spremi promjene') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
