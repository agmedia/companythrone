@extends('layouts.app')

@section('title', __('Moj profil'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <h1 class="h4 mb-4 mt-1">{{ __('Moj profil') }}</h1>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="post" action="{{ route('account.profile.update') }}" class="vstack gap-3">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('Korisničko ime') }}</label>
                                    <input id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('E-mail') }}</label>
                                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ime</label>
                                    <input type="text" name="fname" class="form-control" value="{{ old('fname',$user->detail->fname ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Prezime</label>
                                    <input type="text" name="lname" class="form-control" value="{{ old('lname',$user->detail->lname ?? '') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Telefon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->detail->phone ?? '') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Adresa</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address',$user->detail->address ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Poštanski broj</label>
                                    <input type="text" name="zip" class="form-control" value="{{ old('zip',$user->detail->zip ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Grad</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city',$user->detail->city ?? '') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Država</label>
                                    <input type="text" name="state" class="form-control" value="{{ old('state',$user->detail->state ?? '') }}">
                                </div>
                                <div class="col-md-12 mb-3 mt-2" style="display: block;">
                                    <button type="submit" class="btn btn-lg btn-primary">{{ __('settings.save') }}</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <livewire:settings.password :user="auth()->user()" />
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
