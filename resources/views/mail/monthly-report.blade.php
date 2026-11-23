@php
    $text = 'margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #1F3550;';
    $messages = $statistics->messageSummary();
@endphp

<x-mail.layout :preheader="__('emails.report.preheader', ['month' => $monthName])" :tagline="__('emails.report.tagline')">
    <tr>
        <td class="mail-pad" style="padding: 40px 40px 8px;">
            <h1 style="margin: 0 0 16px; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 28px; line-height: 1.2; color: #001B48;">{{ __('emails.report.title', ['month' => $monthName]) }}</h1>
            <p style="{{ $text }}">{{ __('emails.report.intro') }}</p>
            <p style="{{ $text }}">{{ __('emails.report.highlights', [
                'visits' => $statistics->visits() ?? '—',
                'messages' => $messages['total'],
                'downloads' => $statistics->resumeDownloads()['total'] ?? '—',
            ]) }}</p>
            <a href="{{ route('dashboard') }}" style="display: inline-block; margin: 8px 0 24px; padding: 14px 26px; background: #001B48; color: #FFFFFF; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 16px;">{{ __('emails.report.action') }}</a>
        </td>
    </tr>

    <x-slot:footer>{{ __('emails.report.footer', ['site' => 'delfinbeta.tech']) }}</x-slot:footer>
</x-mail.layout>
