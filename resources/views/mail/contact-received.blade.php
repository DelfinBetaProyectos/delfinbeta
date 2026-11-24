@php
    $mono = "font-family: 'Fira Code', Consolas, Menlo, monospace; font-size: 12px;";
    $row = 'padding: 12px 0; border-top: 1px solid #C9DAE6;';
    $topic = $contactMessage->topic;
    $hours = config('site.reply_within_hours');
    $mailto = $contactMessage->replyMailto();
@endphp

<x-mail.layout :preheader="Str::limit($contactMessage->message, 80).' · '.__('emails.received.preheader', ['hours' => $hours])" :tagline="__('emails.brand_tagline')">
    <tr>
        <td class="mail-pad" style="padding: 36px 40px 8px;">
            <span style="display: inline-block; padding: 6px 12px; background: #DDE8F0; border-radius: 999px; {{ $mono }} color: #004581; text-transform: uppercase;">{{ __('emails.received.topic_badge', ['topic' => $topic->label()]) }}</span>
            <h1 style="margin: 16px 0 6px; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 26px; line-height: 1.25; color: #001B48;">{{ __('emails.received.title', ['name' => $contactMessage->firstName()]) }}</h1>
            <p style="margin: 0; font-size: 14px; color: #35506A;">{{ __('emails.received.meta', ['date' => $sentAt, 'locale' => $localeName]) }}</p>
        </td>
    </tr>
    <tr>
        <td class="mail-pad" style="padding: 24px 40px 8px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 15px;">
                <tr>
                    <td width="120" style="{{ $row }} {{ $mono }} color: #35506A; text-transform: uppercase;">{{ __('emails.received.name') }}</td>
                    <td style="{{ $row }} color: #001B48; font-weight: 500;">{{ $contactMessage->name }}</td>
                </tr>
                <tr>
                    <td style="{{ $row }} {{ $mono }} color: #35506A; text-transform: uppercase;">{{ __('emails.received.email') }}</td>
                    <td style="{{ $row }}"><a href="mailto:{{ $contactMessage->email }}" style="color: #004581;">{{ $contactMessage->email }}</a></td>
                </tr>
                <tr>
                    <td style="{{ $row }} {{ $mono }} color: #35506A; text-transform: uppercase;">{{ __('emails.received.topic') }}</td>
                    <td style="{{ $row }} color: #001B48;">{{ $topic->isProject() ? __('emails.received.project_topic', ['topic' => $topic->label()]) : $topic->label() }}</td>
                </tr>
                <tr>
                    <td style="{{ $row }} border-bottom: 1px solid #C9DAE6; {{ $mono }} color: #35506A; text-transform: uppercase;">{{ __('emails.received.page') }}</td>
                    <td style="{{ $row }} border-bottom: 1px solid #C9DAE6; color: #001B48;">{{ $contactMessage->page }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="mail-pad" style="padding: 20px 40px 8px;">
            <p style="margin: 0 0 10px; {{ $mono }} color: #35506A; text-transform: uppercase;">{{ __('emails.received.message') }}</p>
            <div style="padding: 20px 24px; background: #DDE8F0; border-radius: 8px; font-size: 15px; line-height: 1.65; color: #1F3550;">{!! nl2br(e($contactMessage->message), false) !!}</div>
        </td>
    </tr>
    <tr>
        <td class="mail-pad" style="padding: 24px 40px {{ $topic->isProject() ? '8px' : '36px' }};">
            <a href="{{ $mailto }}" style="display: inline-block; padding: 14px 26px; background: #001B48; color: #FFFFFF; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 16px;">{{ __('emails.received.reply', ['name' => $contactMessage->firstName()]) }}</a>
        </td>
    </tr>
    @if ($topic->isProject())
        <tr>
            <td class="mail-pad" style="padding: 20px 40px 36px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: separate; border: 1px solid #97CBDC; border-radius: 8px;">
                    <tr>
                        <td style="padding: 18px 22px; font-size: 14px; line-height: 1.6; color: #1F3550;">
                            {!! __('emails.received.project_note', ['email' => '<a href="mailto:'.e(config('site.araguaneycode_email')).'" style="color: #004581; font-weight: 500;">'.e(config('site.araguaneycode_email')).'</a>']) !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    <x-slot:footer>
        <span style="{{ $mono }} font-size: 11px;">{{ __('emails.received.footer', ['reference' => $contactMessage->reference()]) }}</span>
    </x-slot:footer>
</x-mail.layout>
