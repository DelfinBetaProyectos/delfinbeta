<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Language settings')] class extends Component {
    use ProfileValidationRules;

    public string $locale = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->locale = Auth::user()->locale;
    }

    /**
     * Save the new language as soon as it is picked, then reload so the whole page uses it.
     */
    public function updatedLocale(): void
    {
        $validated = $this->validate(['locale' => $this->localeRules()]);

        Auth::user()->update($validated);
        session()->put('locale', $validated['locale']);

        $this->redirectRoute('language.edit', navigate: true);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Language settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Language')" :subheading="__('Choose the language for the site and your account')">
        <flux:radio.group wire:model.live="locale" variant="segmented" aria-label="{{ __('Language') }}">
            @foreach (config('site.locales') as $supportedLocale)
                <flux:radio :value="$supportedLocale" lang="{{ $supportedLocale }}">{{ config("site.locale_names.{$supportedLocale}") }}</flux:radio>
            @endforeach
        </flux:radio.group>

        <flux:error name="locale" />
    </x-pages::settings.layout>
</section>
