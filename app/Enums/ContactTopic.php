<?php

namespace App\Enums;

/**
 * What a visitor wants to talk about, picked in the home contact form.
 * Labels live in lang/{locale}/home.php under `contact.form.topics`.
 */
enum ContactTopic: string
{
    case Project = 'project';
    case Talk = 'talk';
    case Role = 'role';
    case Other = 'other';

    /**
     * The topic as shown in the form and in the emails, in the current (or given) locale.
     */
    public function label(?string $locale = null): string
    {
        return __("home.contact.form.topics.{$this->value}", [], $locale);
    }

    /**
     * Short name for tables and reports ("Proyecto", "Rol remoto"…), in the current (or given) locale.
     */
    public function shortLabel(?string $locale = null): string
    {
        return __("emails.received.tags.{$this->value}", [], $locale);
    }

    /**
     * Project inquiries are handled by AraguaneyCode, so they get a copy.
     */
    public function isProject(): bool
    {
        return $this === self::Project;
    }
}
