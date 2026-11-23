{{--
    Site template for Laravel notifications (MailMessage::view('mail.notification')).
    Receives the MailMessage data: greeting, introLines, actionText, actionUrl, outroLines, salutation…
--}}
@php
    $text = 'margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #1F3550;';
@endphp

<x-mail.layout :preheader="$introLines[0] ?? $subject ?? config('app.name')">
    <tr>
        <td class="mail-pad" style="padding: 40px 40px 8px;">
            <h1 style="margin: 0 0 16px; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 28px; line-height: 1.2; color: #001B48;">{{ $greeting ?? __('emails.notification.default_greeting') }}</h1>

            @foreach ($introLines as $line)
                <p style="{{ $text }}">{{ $line }}</p>
            @endforeach
        </td>
    </tr>

    @isset($actionText)
        <tr>
            <td class="mail-pad" style="padding: 8px 40px 24px;">
                <a href="{{ $actionUrl }}" style="display: inline-block; padding: 14px 26px; background: {{ $level === 'error' ? '#B42318' : '#001B48' }}; color: #FFFFFF; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 16px;">{{ $actionText }}</a>
            </td>
        </tr>
    @endisset

    <tr>
        <td class="mail-pad" style="padding: 0 40px 8px;">
            @foreach ($outroLines as $line)
                <p style="{{ $text }}">{{ $line }}</p>
            @endforeach

            <p style="margin: 8px 0 0; {{ $text }}">
                @if (! empty($salutation))
                    {{ $salutation }}
                @else
                    {{ __('emails.notification.salutation') }}<br>
                    <span style="font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; color: #001B48;">Dayan Betancourt</span>
                @endif
            </p>
        </td>
    </tr>

    @isset($actionText)
        <tr>
            <td class="mail-pad" style="padding: 16px 40px 36px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; border-top: 1px solid #C9DAE6;">
                    <tr>
                        <td style="padding-top: 20px; font-size: 13px; line-height: 1.6; color: #35506A;">
                            {{ __('emails.notification.trouble', ['action' => $actionText]) }}<br>
                            <a href="{{ $actionUrl }}" style="color: #004581; word-break: break-all;">{{ $displayableActionUrl }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @else
        <tr><td style="padding: 0 0 28px;">&nbsp;</td></tr>
    @endisset

    <x-slot:footer>
        {!! __('emails.notification.footer', ['site' => '<a href="'.e(route('home')).'" style="color: #97CBDC;">delfinbeta.tech</a>']) !!}
        {{ __('emails.location') }}
    </x-slot:footer>
</x-mail.layout>
