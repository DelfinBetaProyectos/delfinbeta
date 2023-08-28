@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block py-2 pr-4 pl-3 rounded text-white lg:text-delfinbeta-medium bg-delfinbeta-medium_dark lg:bg-transparent lg:p-0 dark:text-white'
            : 'block py-2 pr-4 pl-3 text-delfinbeta-medium_dark border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-delfinbeta-medium lg:p-0 dark:text-delfinbeta-medium_light lg:dark:hover:text-white dark:hover:bg-delfinbeta-medium_dark dark:hover:text-white lg:dark:hover:bg-transparent dark:border-delfinbeta-medium_dark';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
