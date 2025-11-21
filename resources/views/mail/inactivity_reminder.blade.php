@extends('mail.layouts.base')

@section('title', __('Podsjetnik'))

@section('content')
    <h2 style="margin:0 0 12px 0; font-size:22px; line-height:1.3; font-weight:700;">
        {{ __('Podsjetnik') }}
    </h2>

    <p>Poštovani {{ $user->name }},</p>

    <p>redovnim dnevnim aktivnostima na platformi Companythrone dobit ćete više
        posjeta na Vašu web stranicu. </p>

    <p>Kada se <a href="{{ route('login') }}">logirate</a> na Companythrone u nadzornoj ploči pogledajte  - Istraži stranice.</p>

    <p>U Trenutnim  preporukama naći ćete brojeve koji vode do stranica naših partnera.</p>
    <p>Nakon pregleda preporučenih stranica bit će aktivna poveznica na Vašu stranicu kako bi i drugi mogli vidjeti Vašu ponudu.</p>

    <p><a href="{{ route('unsubscribe.reminder', ['user' => $user->id, 'token' => sha1($user->email)]) }}">
            Odjava – klikni ovdje ako više ne želiš dobivati ovaj podsjetnik
        </a></p>

    <p style="margin:25px 0 0 0;">
        {{ __('Srdačan pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
