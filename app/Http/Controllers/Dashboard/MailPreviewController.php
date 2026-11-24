<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ContactTopic;
use App\Http\Controllers\Controller;
use App\Mail\ContactConfirmation;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;

/**
 * Browser previews of the site's emails, filled with sample data (nothing is saved or sent).
 * Laravel renders a mailable returned from a route as HTML.
 */
class MailPreviewController extends Controller
{
    /**
     * Preview the notification Dayan receives for a contact form message.
     */
    public function contactReceived(ContactTopic $topic): ContactReceived
    {
        return new ContactReceived($this->sampleContactMessage($topic, 'es'));
    }

    /**
     * Preview the confirmation the sender receives, in their language.
     */
    public function contactConfirmation(string $locale, ContactTopic $topic): ContactConfirmation
    {
        return new ContactConfirmation($this->sampleContactMessage($topic, $locale));
    }

    /**
     * Preview the password reset email in the given language, addressed to the signed-in user.
     * The link uses a dummy token, so it does not reset anything.
     */
    public function passwordReset(Request $request, string $locale): MailMessage
    {
        App::setLocale($locale);

        return (new ResetPasswordNotification('preview-token'))->toMail($request->user());
    }

    /**
     * Preview the email verification email in the given language, addressed to the signed-in user.
     * The signed link is real but only verifies the address of whoever is already signed in.
     */
    public function verifyEmail(Request $request, string $locale): MailMessage
    {
        App::setLocale($locale);

        return (new VerifyEmailNotification)->toMail($request->user());
    }

    /**
     * An unsaved contact message with realistic sample content.
     */
    private function sampleContactMessage(ContactTopic $topic, string $locale): ContactMessage
    {
        $contactMessage = new ContactMessage([
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'topic' => $topic,
            'message' => __('emails.preview.message', [], $locale),
            'locale' => $locale,
            'page' => '/#'.__('nav.sections.contact.anchor', [], $locale),
        ]);

        return $contactMessage->forceFill(['id' => 123, 'created_at' => now()]);
    }
}
