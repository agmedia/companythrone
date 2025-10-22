<?php

namespace App\Mail;

use App\Models\Back\Catalog\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentOfferMail extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels;

    public function __construct(public Company $company)
    {
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.payment_offer_subject', ['company' => $this->company->name]),
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-offer',
            with: [
                'company' => $this->company,
                'amount'  => number_format(app_settings()->getPrice(), 2) . ' €',
            ],
        // or: markdown: 'mail.payment-offer' if you prefer Markdown
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
