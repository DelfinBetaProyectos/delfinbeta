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
 * Notifies Dayan of a new contact form message, always in Spanish. Replying answers the sender,
 * and project inquiries are copied to AraguaneyCode.
 */
class ContactReceived extends Mailable implements ShouldQueue
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
        $this->locale('es');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [config('site.email')],
            cc: $this->contactMessage->topic->isProject() ? [config('site.araguaneycode_email')] : [],
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: __('emails.received.subject', [
                'tag' => __("emails.received.tags.{$this->contactMessage->topic->value}"),
                'name' => $this->contactMessage->firstName(),
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-received',
            with: [
                'sentAt' => $this->contactMessage->created_at
                    ->timezone(config('site.timezone'))
                    ->locale('es')
                    ->isoFormat('D MMM YYYY, h:mm a'),
                'localeName' => __("emails.received.locales.{$this->contactMessage->locale}"),
            ],
        );
    }
}
