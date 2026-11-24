@props([
    'type' => 'isotipo',
    'tone' => 'auto',
    'label' => null,
])

{{--
    DelfinBeta brand mark (isotipo, logotipo or full logo).
    tone="auto" shows the blue version in light mode and the white one in dark mode;
    tone="white" always shows the white version (for dark backgrounds).
    Give it an explicit width and height; the image is contained inside that box.
--}}
@php
    $baseName = match ($type) {
        'logotipo' => 'DelfinBeta_Logotipo',
        'logo' => 'DelfinBeta_Logo',
        default => 'DelfinBeta_Isotipo',
    };
    $whiteSrc = asset("images/brand/{$baseName}_White.png");
    $blueSrc = asset("images/brand/{$baseName}.png");
    $alt = $label ?? '';
    $classes = $attributes->class('shrink-0 object-contain');
@endphp

@if ($tone === 'white')
    <img src="{{ $whiteSrc }}" alt="{{ $alt }}" {{ $classes }}>
@else
    <img src="{{ $blueSrc }}" alt="{{ $alt }}" {{ $classes->class('dark:hidden') }}>
    <img src="{{ $whiteSrc }}" alt="{{ $alt }}" {{ $classes->class('hidden dark:block') }}>
@endif
