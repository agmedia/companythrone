@extends('mail.layouts.base')

@section('title', __('Nova poruka s kontakt forme'))

@section('content')
    @php
        $safeName    = e($name ?? '-');
        $safeEmail   = e($email ?? '-');
        $hasSubject  = !empty($subjectText ?? null);
        $safeSubject = e($hasSubject ? $subjectText : __('(bez predmeta)'));
        $replySubject = rawurlencode('Re: '.($hasSubject ? $subjectText : __('Nova poruka s kontakt forme')));
        // Ako želiš dodati i default body:
        // $replyBody = rawurlencode("\n\n----\n".($messageText ?? ''));
    @endphp

    <h2 style="margin:0 0 12px 0; font-size:22px; line-height:1.3; font-weight:700;">
        {{ __('Nova poruka s kontakt forme') }}
    </h2>

    <p style="margin:0 0 12px 0;">
        {{ __('Primili ste novu poruku s kontakt forme na web stranici.') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:25px 0;">
        <tr>
            <td style="padding:6px 0; font-weight:600; width:42%; vertical-align:top;">
                {{ __('Ime i prezime:') }}
            </td>
            <td style="padding:6px 0; vertical-align:top; word-break:break-word;">
                {{ $safeName }}
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0; font-weight:600; vertical-align:top;">
                {{ __('E-pošta:') }}
            </td>
            <td style="padding:6px 0; vertical-align:top; word-break:break-all;">
                @if(!empty($email))
                    <a href="mailto:{{ $email }}?subject={{ $replySubject }}" style="color:#c92d2d; text-decoration:none;">
                        {{ $safeEmail }}
                    </a>
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0; font-weight:600; vertical-align:top;">
                {{ __('Predmet poruke:') }}
            </td>
            <td style="padding:6px 0; vertical-align:top; word-break:break-word;">
                {{ $safeSubject }}
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 8px 0; font-weight:600;">
        {{ __('Poruka:') }}
    </p>
    <div style="background:#f9fafb; border-radius:6px; padding:15px; border:1px solid #e5e7eb;">
        {!! nl2br(e($messageText ?? '')) !!}
    </div>

    <!-- CTA: Odgovori -->
    @if(!empty($email))
        <div style="text-align:center; margin-top:24px;">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml"
                         href="mailto:{{ $email }}?subject={{ $replySubject }}"
                         style="height:44px; v-text-anchor:middle; width:220px;"
                         arcsize="10%" fillcolor="#c92d2d" stroked="f">
                <w:anchorlock/>
                <center style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:700;">
                    {{ __('Odgovori na poruku') }}
            </center>
        </v:roundrect>
<![endif]-->
            <!--[if !mso]><!-- -->
            <a href="mailto:{{ $email }}?subject={{ $replySubject }}"
               style="background-color:#c92d2d; color:#ffffff; display:inline-block; padding:12px 22px; border-radius:6px;
                      text-decoration:none; font-weight:700; font-size:16px; line-height:1; min-width:200px; text-align:center;">
                {{ __('Odgovori na poruku') }}
            </a>
            <!--<![endif]-->
        </div>
    @endif

    <p style="margin:25px 0 0 0;">
        {{ __('Poruku je moguće odgovoriti izravno na:') }}
        @if(!empty($email))
            <a href="mailto:{{ $email }}?subject={{ $replySubject }}" style="color:#c92d2d; text-decoration:none; word-break:break-all;">
                {{ $safeEmail }}
            </a>
        @else
            —
        @endif
    </p>

    <p style="margin:25px 0 0 0;">
        {{ __('Lijep pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
