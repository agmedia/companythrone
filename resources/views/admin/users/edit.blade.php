@extends('admin.layouts.base-admin')

@php $isEdit = isset($user) && $user->exists; @endphp

@section('title', $isEdit ? 'Uredi korisnika' : 'Dodaj korisnika')

@section('content')
    <form method="POST" action="{{ $isEdit ? route('users.update',$user) : route('users.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $isEdit ? 'Uredi korisnika' : 'Dodaj korisnika' }}</h5>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ime</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$user->name ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$user->email ?? '') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Ime (fname)</label>
                    <input type="text" name="fname" class="form-control" value="{{ old('fname',$user->detail->fname ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prezime</label>
                    <input type="text" name="lname" class="form-control" value="{{ old('lname',$user->detail->lname ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->detail->phone ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Adresa</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address',$user->detail->address ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Poštanski broj</label>
                    <input type="text" name="zip" class="form-control" value="{{ old('zip',$user->detail->zip ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grad</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city',$user->detail->city ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Država</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state',$user->detail->state ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Uloga</label>
                    <select name="role" class="form-select">
                        @foreach(['admin','editor','company_owner','customer'] as $role)
                            <option value="{{ $role }}" @selected(old('role',$user->detail->role ?? '') === $role)>{{ __('back/users.tabs.'.$role) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label><br>
                    <input type="checkbox" name="status" value="1" @checked(old('status',$user->detail->status ?? true))> Aktivan
                </div>

            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary">{{ $isEdit ? 'Spremi promjene' : 'Dodaj korisnika' }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Odustani</a>
            </div>
        </div>
    </form>

    @if ($isEdit)
        <div class="row g-3">
            <div class="col-md-6">
                <livewire:settings.password :user="$user" />
            </div>
        </div>
    @endif
@endsection
