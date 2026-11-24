<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Laravel's email verification email, using the site's own email template and greeting the user by name.
 */
class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return $this->buildMailMessage($this->verificationUrl($notifiable))
            ->view('mail.notification')
            ->greeting(__('emails.notification.greeting', ['name' => $notifiable->firstname]));
    }
}
