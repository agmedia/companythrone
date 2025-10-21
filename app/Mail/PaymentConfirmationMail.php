<?php

namespace App\Mail;

use App\Models\Back\Billing\Subscription;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Company $company,
        public Subscription $subscription,
        public Payment $payment
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.payment_confirm_subject', ['company' => $this->company->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-confirm',
            with: [
                'company' => $this->company,
                'amount'  => number_format($this->payment->amount, 2) .' €',
            ],
        // or: markdown: 'mail.payment-offer' if you prefer Markdown
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
