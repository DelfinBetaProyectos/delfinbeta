<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Laravel's password reset email, using the site's own email template and greeting the user by name.
 */
class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return $this->buildMailMessage($this->resetUrl($notifiable))
            ->view('mail.notification')
            ->greeting(__('emails.notification.greeting', ['name' => $notifiable->firstname]));
    }
}
