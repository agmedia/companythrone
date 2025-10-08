<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class ReferralInvitationMail extends Mailable
{
    public function __construct(
        public $user,
        public string $referralUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Pozivnica od :name', ['name' => $this->user->name])
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invitation',
            with: [
                'user' => $this->user,
                'referralUrl' => $this->referralUrl,
            ],
        );
    }
}
