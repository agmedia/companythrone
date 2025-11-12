@extends('mail.layouts.base')

@section('title', __('Pozivnica od :name', ['name' => $user->name]))

@section('content')
    <h2 style="margin:0 0 12px 0; font-size:22px; line-height:1.3; font-weight:700;">
        {{ __('Pozivnica') }}
    </h2>

    <p style="margin:0 0 12px 0;">{{ __('Poštovani!') }}</p>

    <p style="margin:0 0 16px 0;">
        {{ __(':name  iz tvrtke  :company_name Vas poziva da se upišete na platformu  :app.', [
            'name' => trim(($user->detail->fname ?? '') . ' ' . ($user->detail->lname ?? '')) ? : $user->name,
            'app' => config('app.name'),
            'company_name' => e($company->t_name ?? $company->name ?? '-')
        ]) }}
    </p>
    <p><strong>Prednosti korištenja platforme:</strong></p>

    <ul>
    <li><strong>Napredna pretraga</strong> – pronalaženje tvrtki po nazivu, OIB-u ili ključnim riječima </li>



        <li><strong> Napredna pretraga</strong> – pronalaženje tvrtki po nazivu, OIB-u ili ključnim riječima</li>

                <li><strong> Novi kontakti</strong> – ostvarite kontakte  s novim kupcima i dobavljačima</li>

                        <li><strong>Jednostavno pokretanje</strong> – brza i jednostavna  registracija</li>

                                <li><strong> Bolja vidljivost</strong> – poboljšana pozicioniranost na tražilicama</li>

                                        <li><strong> Povećani promet</strong> – ostvarite do 780 posjeta na svoju web stranicu</li>





    </ul>


    <p>Radujemo se Vašem sudjelovanju!</p>


    <p style="margin:0 0 16px 0;">
        {{ __('Klikni na gumb ispod kako bi se registrirao putem pozivnice:') }}
    </p>

    <!-- BULLETPROOF BUTTON (VML for Outlook) -->
    <div style="text-align:center; margin:30px 0;">
        <!--[if mso]>
        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $referralUrl }}"
            style="height:44px; v-text-anchor:middle; width:240px;" arcsize="10%" fillcolor="#c92d2d" stroked="f">
            <w:anchorlock/>
            <center style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:700;">
                {{ __('Prihvati pozivnicu') }}
        </center>
    </v:roundrect>
<![endif]-->
        <!--[if !mso]> -->
        <a href="{{ $referralUrl }}"
           style="background-color:#c92d2d; color:#ffffff; display:inline-block; padding:12px 22px; border-radius:6px;
                  text-decoration:none; font-weight:700; font-size:16px; line-height:1; min-width:220px; text-align:center;">
            {{ __('Prihvati pozivnicu') }}
        </a>
        <!--<![endif]-->
    </div>

    <p style="font-size:14px; color:#6b7280; margin:0 0 16px 0;">
        {{ __('Ako gumb ne radi, možeš otvoriti sljedeću poveznicu u pregledniku:') }}
        <br>
        <a href="{{ $referralUrl }}" target="_blank" style="color:#c92d2d; text-decoration:none; word-break:break-all;">
            {{ $referralUrl }}
        </a>
    </p>

    <p style="margin:30px 0 0 0;">
        {{ __('Ovaj poziv je osoban i vrijedi samo za nove registracije.') }}
    </p>

    <p style="margin:25px 0 0 0;">
        {{ __('Srdačan pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
