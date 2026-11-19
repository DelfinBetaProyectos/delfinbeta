@props([
    'on' => 'dark',
])

<span {{ $attributes->class([
    'inline-flex items-center rounded-full border px-3 py-1 font-mono text-xs font-medium',
    'border-line-dark text-muted' => $on === 'dark',
    'border-line-light text-deep' => $on === 'light',
]) }}>{{ $slot }}</span>
