<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactFormMail extends Mailable
{
    /**
     * Podaci iz kontakt forme.
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $subject = null,
        public ?string $messageText = null
    ) {}

    /**
     * Naslov poruke.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Nova poruka s kontakt forme')
        );
    }

    /**
     * Sadržaj maila.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-form',
            with: [
                'name'        => $this->name,
                'email'       => $this->email,
                'subjectText' => $this->subject,
                'messageText' => $this->messageText,
            ],
        );
    }

    /**
     * Prilozi (nije potrebno, ali ostavljeno za mogućnost upload fajla kasnije).
     */
    public function attachments(): array
    {
        return [];
    }
}
