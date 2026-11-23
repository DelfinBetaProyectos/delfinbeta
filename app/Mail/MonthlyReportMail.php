<?php

namespace App\Mail;

use App\Services\MonthlyReport;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * The monthly personal brand report, sent to the site address in Spanish with the PDF attached.
 */
class MonthlyReportMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(public MonthlyReport $report)
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
            subject: __('emails.report.subject', ['month' => $this->report->monthName()]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.monthly-report',
            with: [
                'statistics' => $this->report->statistics,
                'monthName' => $this->report->monthName(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->report->pdf(), $this->report->fileName())->withMime('application/pdf'),
        ];
    }
}
