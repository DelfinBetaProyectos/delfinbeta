@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'border border-ice text-ice hover:bg-ice/10 hover:text-mist',
        'dark' => 'bg-navy text-white hover:bg-deep',
        'outline' => 'border border-deep text-deep hover:bg-deep/5',
        default => 'bg-ice text-navy hover:bg-white',
    };

    $focusRingOffset = in_array($variant, ['dark', 'outline']) ? 'focus-visible:ring-offset-white' : 'focus-visible:ring-offset-navy';

    $classes = "inline-flex min-h-12 items-center justify-center gap-2 rounded-btn px-6 font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sea focus-visible:ring-offset-2 {$focusRingOffset} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
