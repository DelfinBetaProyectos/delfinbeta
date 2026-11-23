@props([
    'value' => 0,
    'max' => 1,
    'expected' => null,
])

{{--
    Horizontal bar for a figure against its goal. `expected` (0–1) marks where the figure should be
    today. Decorative: the figures are always written next to it.
--}}
@php
    $fill = $max > 0 ? min(100, round(($value ?? 0) * 100 / $max)) : 0;
@endphp

<div {{ $attributes->class('relative h-1.5 rounded-full bg-mist dark:bg-white/10') }} aria-hidden="true">
    <div class="h-full rounded-full bg-deep dark:bg-ice" style="width: {{ $fill }}%"></div>

    @if ($expected !== null)
        <div class="absolute -top-1 h-3.5 w-0.5 rounded-full bg-navy dark:bg-white" style="left: {{ min(100, round($expected * 100, 1)) }}%"></div>
    @endif
</div>
