<footer {{ $attributes->class('border-t border-line-subtle') }}>
    <div class="mx-auto flex max-w-site flex-wrap justify-between gap-4 px-4 py-8 text-sm text-muted sm:px-6">
        <p>&copy; {{ now()->year }} Dayan Betancourt · {{ __('footer.tagline') }}</p>
        <p class="font-mono text-[13px]">
            {{ __('footer.built_with') }} ·
            <a href="{{ config('site.source_url') }}" rel="noopener" target="_blank" class="text-ice underline underline-offset-4 hover:text-mist">{{ __('footer.source') }}</a>
        </p>
    </div>
</footer>
