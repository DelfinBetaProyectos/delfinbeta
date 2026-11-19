@props([
    'period',
    'title',
])

<li {{ $attributes->class('flex flex-wrap gap-x-8 gap-y-2 border-t border-line-light py-7 last:border-b') }}>
    <p class="flex-[0_0_10rem] font-mono text-sm font-medium text-deep">{{ $period }}</p>

    <div class="min-w-0 flex-[1_1_26rem]">
        <h3 class="mb-1.5 font-display text-[22px] leading-snug font-bold text-navy">{{ $title }}</h3>
        <div class="text-ink-strong">{{ $slot }}</div>
    </div>
</li>
