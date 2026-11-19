@props([
    'text' => null,
])

@php
    $years = (int) floor(\Illuminate\Support\Facades\Date::parse(config('site.career_started_at'))->diffInYears(now()));
@endphp

{{ $text ? str_replace(':years', $years, $text) : $years }}
