@props([
    'value',
    'label',
])

<div {{ $attributes->class('rounded-xl border border-line-light bg-white p-5') }}>
    <p class="font-display text-[2.125rem] leading-tight font-bold text-deep">{{ $value }}</p>
    <p class="text-sm text-ink">{{ $label }}</p>
</div>
