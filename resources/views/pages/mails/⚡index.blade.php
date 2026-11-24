<?php

use App\Enums\ContactTopic;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Mails')] class extends Component {
    /**
     * One row per email (and topic variant), with its preview URL per language.
     * A language the email is never sent in has no URL.
     *
     * @return list<array{mail: string, variant: string|null, recipient: string, urls: array<string, string|null>}>
     */
    #[Computed]
    public function previews(): array
    {
        $locales = config('site.locales');
        $topics = [
            ContactTopic::Talk->value => __('Any topic'),
            ContactTopic::Project->value => __('Project (copied to AraguaneyCode)'),
        ];

        $previews = [];

        foreach ($topics as $topic => $variant) {
            $previews[] = [
                'mail' => __('Contact form notification'),
                'variant' => $variant,
                'recipient' => __('To me (always in Spanish)'),
                'urls' => collect($locales)->mapWithKeys(fn (string $locale): array => [
                    $locale => $locale === 'es' ? route('dashboard.mails.contact-received', $topic) : null,
                ])->all(),
            ];
        }

        foreach ($topics as $topic => $variant) {
            $previews[] = [
                'mail' => __('Contact form confirmation'),
                'variant' => $variant,
                'recipient' => __('To the sender, in the language they used'),
                'urls' => collect($locales)->mapWithKeys(fn (string $locale): array => [
                    $locale => route('dashboard.mails.contact-confirmation', [$locale, $topic]),
                ])->all(),
            ];
        }

        foreach (['password-reset' => __('Password reset'), 'verify-email' => __('Email verification')] as $routeName => $mail) {
            $previews[] = [
                'mail' => $mail,
                'variant' => null,
                'recipient' => __('To the user, in their saved language'),
                'urls' => collect($locales)->mapWithKeys(fn (string $locale): array => [
                    $locale => route("dashboard.mails.{$routeName}", $locale),
                ])->all(),
            ];
        }

        return $previews;
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Mails') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Previews of the emails the site sends, with sample data') }}</flux:subheading>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Variant') }}</flux:table.column>
            <flux:table.column>{{ __('Recipient') }}</flux:table.column>
            @foreach (config('site.locales') as $locale)
                <flux:table.column align="center">{{ config("site.locale_names.{$locale}") }}</flux:table.column>
            @endforeach
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->previews as $preview)
                <flux:table.row :key="$loop->index">
                    <flux:table.cell variant="strong">{{ $preview['mail'] }}</flux:table.cell>
                    <flux:table.cell>{{ $preview['variant'] ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $preview['recipient'] }}</flux:table.cell>
                    @foreach ($preview['urls'] as $locale => $url)
                        <flux:table.cell align="center">
                            @if ($url)
                                <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="$url" target="_blank" inset="top bottom" :aria-label="__('Preview').' · '.$preview['mail'].' · '.config('site.locale_names.'.$locale)">{{ __('Preview') }}</flux:button>
                            @else
                                <span class="text-zinc-400" title="{{ __('Not sent in this language') }}">—</span>
                            @endif
                        </flux:table.cell>
                    @endforeach
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:text class="mt-4">{{ __('Previews open in a new tab and nothing is sent.') }}</flux:text>
</section>
