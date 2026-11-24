<?php

namespace App\Models\Concerns;

/**
 * Stores each attribute listed in `$translatable` as a JSON object keyed by locale
 * ({"es": "...", "en": "..."}). Reading the attribute returns the value for the
 * current locale, falling back to the site's first locale when it is empty.
 *
 * The model must cast every translatable attribute to `array`.
 */
trait HasTranslations
{
    /**
     * Get an attribute, resolving translatable ones to the current locale.
     */
    public function getAttributeValue($key): mixed
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key);
    }

    /**
     * Get an attribute's value in the given (or current) locale, falling back to the site's first locale.
     */
    public function getTranslation(string $key, ?string $locale = null): mixed
    {
        $translations = $this->getTranslations($key);
        $locale ??= app()->getLocale();
        $fallbackLocale = config('site.locales')[0];

        if (filled($translations[$locale] ?? null)) {
            return $translations[$locale];
        }

        return $translations[$fallbackLocale] ?? null;
    }

    /**
     * Get every locale's value for a translatable attribute.
     *
     * @return array<string, mixed>
     */
    public function getTranslations(string $key): array
    {
        return parent::getAttributeValue($key) ?? [];
    }

    /**
     * Determine whether the attribute is stored per locale.
     */
    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->translatable, true);
    }
}
