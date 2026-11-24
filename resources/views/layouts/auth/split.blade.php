@php
    $quote = collect(__('quotes.items'))->random();

    $topAction = match (true) {
        request()->routeIs('login') && Route::has('register') => ['prompt' => __('First time here?'), 'label' => __('Create an account'), 'href' => route('register')],
        request()->routeIs('register') => ['prompt' => __('Already have an account?'), 'label' => __('Log in'), 'href' => route('login')],
        default => null,
    };

    $chartBars = [38, 52, 45, 68, 60, 82, 75];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="grid min-h-dvh lg:grid-cols-2">
            {{-- Brand panel --}}
            <aside class="relative hidden flex-col overflow-hidden bg-navy ps-10 pt-12 lg:flex xl:ps-[72px]">
                <a href="{{ route('home') }}" class="-ms-1 w-fit" wire:navigate>
                    <x-brand-mark type="logo" tone="white" :label="config('app.name')" class="-my-8 h-36 w-48" />
                </a>

                <div class="mt-8 max-w-xl pe-10">
                    <h2 class="font-display text-[40px] leading-[1.15] font-bold tracking-[-0.02em] text-white">
                        {{ __('Everything about your brand, at a glance.') }}
                    </h2>
                    <p class="mt-4 text-lg text-muted">
                        {{ __('Your content, audience and goals — waiting for you on the other side.') }}
                    </p>
                </div>

                {{-- Decorative dashboard preview --}}
                <div aria-hidden="true" class="mt-10 flex flex-1 overflow-hidden rounded-tl-2xl bg-zinc-50">
                    <div class="flex w-16 shrink-0 flex-col items-center gap-4 border-e border-zinc-200 bg-white pt-6">
                        <span class="size-7 rounded-md bg-navy"></span>
                        @foreach (range(1, 4) as $item)
                            <span @class(['size-5 rounded', 'bg-mist' => $loop->first, 'bg-zinc-100' => ! $loop->first])></span>
                        @endforeach
                    </div>

                    <div class="flex flex-1 flex-col gap-5 p-7">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-zinc-900">{{ __('Overview') }}</span>
                            <span class="rounded-md border border-zinc-200 bg-white px-2.5 py-1 text-xs text-zinc-600">{{ __('Last 7 days') }}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            @foreach ([__('Followers') => 'w-3/4', __('Engagement') => 'w-1/2', __('Posts') => 'w-2/5'] as $label => $width)
                                <div class="rounded-lg border border-zinc-200 bg-white p-4">
                                    <span class="text-xs text-zinc-600">{{ $label }}</span>
                                    <span class="mt-2 block h-4 rounded bg-mist {{ $width }}"></span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-1 flex-col rounded-lg border border-zinc-200 bg-white p-4 pb-0">
                            <span class="text-xs font-semibold text-zinc-900">{{ __('Reach') }}</span>
                            <div class="mt-4 flex flex-1 items-end gap-3">
                                @foreach ($chartBars as $height)
                                    <span @class(['flex-1', 'bg-deep' => $height === max($chartBars), 'bg-ice/50' => $height !== max($chartBars)]) style="height: {{ $height }}%"></span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Form column --}}
            <div class="flex min-h-dvh flex-col px-6 py-8 sm:px-12">
                <div class="flex min-h-6 items-center justify-end gap-1.5 text-sm">
                    @if ($topAction)
                        <span class="text-zinc-600 dark:text-zinc-400">{{ $topAction['prompt'] }}</span>
                        <flux:link :href="$topAction['href']" class="font-semibold" wire:navigate>{{ $topAction['label'] }}</flux:link>
                    @endif
                </div>

                <main class="mx-auto flex w-full max-w-[400px] flex-1 flex-col justify-center py-10">
                    <a href="{{ route('home') }}" class="mx-auto mb-8 lg:hidden" wire:navigate>
                        <x-brand-mark type="logo" :label="config('app.name')" class="-my-8 h-36 w-48" />
                    </a>

                    {{ $slot }}

                    <figure class="mt-9 border-t border-zinc-200 pt-7 text-center dark:border-zinc-700">
                        <blockquote class="text-zinc-600 italic dark:text-zinc-400">&ldquo;{{ $quote['text'] }}&rdquo;</blockquote>
                        <figcaption class="mt-1 font-mono text-[13px] text-zinc-500">— {{ $quote['author'] }}</figcaption>
                    </figure>
                </main>

                <footer class="text-[13px] text-zinc-500">
                    &copy; {{ now()->year }} {{ config('app.name') }}
                </footer>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
