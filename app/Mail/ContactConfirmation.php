<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms to the sender that their message arrived, in the language they used on the site.
 */
class ContactConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Attempts before the job is marked as failed (a stuck SMTP connection is usually temporary).
     */
    public int $tries = 3;

    /**
     * Seconds to wait before each retry.
     *
     * @var list<int>
     */
    public array $backoff = [10, 60];

    /**
     * Seconds an attempt may run; below the worker's 60 s limit so a stuck attempt fails and retries.
     */
    public int $timeout = 30;

    /**
     * Create a new message instance.
     */
    public function __construct(public ContactMessage $contactMessage)
    {
        $this->locale($contactMessage->locale);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            replyTo: [config('site.email')],
            subject: __('emails.confirmation.subject', ['name' => $this->contactMessage->firstName()]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: 'mail.contact-confirmation');
    }
}
