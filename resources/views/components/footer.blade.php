<footer {{ $attributes->class('border-t border-line-dark bg-navy') }}>
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-1">
            <p class="font-display text-lg font-bold text-white">Dayan Betancourt</p>
            <p class="text-sm text-muted">&copy; {{ now()->year }} delfinbeta.tech</p>
        </div>

        <ul class="flex flex-wrap items-center gap-6">
            @foreach (array_filter(config('site.social')) as $network => $url)
                <li>
                    <a href="{{ $url }}" rel="me noopener" target="_blank" class="text-sm font-medium text-muted transition-colors hover:text-white">{{ $network }}</a>
                </li>
            @endforeach

            @if (config('site.araguaneycode_url'))
                <li>
                    <x-button variant="secondary" :href="config('site.araguaneycode_url')" rel="noopener" target="_blank">
                        {{ __('footer.work_together') }}
                    </x-button>
                </li>
            @endif
        </ul>
    </div>
</footer>
