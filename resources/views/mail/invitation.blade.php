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
            'company_name' => $company->name
        ]) }}
    </p>

    <p>Pretraga po – Nazivu, OIB-u, ključnim riječima</p>

    <p> Novi kontakti – ostvari kontakte sa novim kupcima i dobavljačima</p>

    <p>Jednostavno pokretanje</p>

    <p>Bolja vidljivost  na tražilicama</p>

    <p>Ostvari do 780 posjeta na svoju web stranicu</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:25px 0;">
        <tr>
            <td style="padding:6px 0; font-weight:600; width:42%; vertical-align:top;">
                {{ __('Tvrtka pošiljatelja:') }}
            </td>
            <td style="padding:6px 0; vertical-align:top;">
                {{ e($company->t_name ?? $company->name ?? '-') }}
            </td>
        </tr>

        @if(!empty($company->weburl))
            <tr>
                <td style="padding:6px 0; font-weight:600; width:42%; vertical-align:top;">
                    {{ __('Web stranica:') }}
                </td>
                <td style="padding:6px 0; vertical-align:top; word-break:break-all;">
                    <a href="{{ $company->weburl }}" target="_blank" style="color:#c92d2d; text-decoration:none;">
                        {{ $company->weburl }}
                    </a>
                </td>
            </tr>
        @endif

        @if(!empty($company->description))
            <tr>
                <td style="padding:6px 0; font-weight:600; width:42%; vertical-align:top;">
                    {{ __('Opis tvrtke:') }}
                </td>
                <td style="padding:6px 0; vertical-align:top;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($company->description), 200) }}
                </td>
            </tr>
        @endif
    </table>

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
