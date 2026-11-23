@php
    $mono = "font-family: 'Fira Code', Consolas, Menlo, monospace; font-size: 12px;";
    $text = 'font-size: 16px; line-height: 1.6; color: #1F3550;';
    $topic = $contactMessage->topic;
    $siteUrl = route('home');
    $socialLinks = collect(config('site.social'))->only(['LinkedIn', 'GitHub', 'Medium', 'DEV'])->filter();
@endphp

<x-mail.layout :preheader="__('emails.confirmation.preheader')">
    <tr>
        <td class="mail-pad" style="padding: 40px 40px 8px;">
            <h1 style="margin: 0 0 16px; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 28px; line-height: 1.2; color: #001B48;">{{ __('emails.confirmation.greeting', ['name' => $contactMessage->firstName()]) }}</h1>
            <p style="margin: 0 0 16px; {{ $text }}">{{ __('emails.confirmation.intro', ['hours' => config('site.reply_within_hours')]) }}</p>
            <p style="margin: 0; {{ $text }}">{{ __('emails.confirmation.summary') }}</p>
        </td>
    </tr>
    <tr>
        <td class="mail-pad" style="padding: 20px 40px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: separate; background: #DDE8F0; border-radius: 8px;">
                <tr><td style="padding: 20px 24px 8px; {{ $mono }} color: #004581; text-transform: uppercase;">{{ __('emails.confirmation.topic') }}</td></tr>
                <tr><td style="padding: 0 24px 16px; font-size: 16px; font-weight: 500; color: #001B48;">{{ $topic->label() }}</td></tr>
                <tr><td style="padding: 0 24px 8px; {{ $mono }} color: #004581; text-transform: uppercase;">{{ __('emails.confirmation.message') }}</td></tr>
                <tr><td style="padding: 0 24px 20px; font-size: 15px; line-height: 1.6; color: #1F3550;">{!! nl2br(e($contactMessage->message), false) !!}</td></tr>
            </table>
        </td>
    </tr>
    @if ($topic->isProject())
        <tr>
            <td class="mail-pad" style="padding: 4px 40px 8px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: separate; border: 1px solid #97CBDC; border-radius: 8px;">
                    <tr>
                        <td style="padding: 18px 22px; font-size: 15px; line-height: 1.6; color: #1F3550;">
                            {!! __('emails.confirmation.project_note', ['agency' => '<a href="'.e(config('site.araguaneycode_url')).'" style="color: #004581; font-weight: 500;">AraguaneyCode</a>']) !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endif
    <tr>
        <td class="mail-pad" style="padding: 24px 40px 8px;">
            <p style="margin: 0 0 20px; {{ $text }}">{{ __('emails.confirmation.meanwhile') }}</p>
            <a href="{{ route('projects.index') }}" style="display: inline-block; padding: 14px 26px; background: #97CBDC; color: #001B48; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 16px;">{{ __('emails.confirmation.projects') }}</a>
            @if (App\Models\Article::hasPublished())
                <a href="{{ route('blog.index') }}" style="display: inline-block; margin-left: 16px; padding: 14px 4px; color: #004581; font-weight: 500; font-size: 16px;">{{ __('emails.confirmation.blog') }} &rarr;</a>
            @endif
        </td>
    </tr>
    <tr>
        <td class="mail-pad" style="padding: 32px 40px 36px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; border-top: 1px solid #C9DAE6;">
                <tr>
                    <td style="padding-top: 24px;">
                        <p style="margin: 0; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 18px; color: #001B48;">Dayan Betancourt</p>
                        <p style="margin: 4px 0 0; font-size: 14px; line-height: 1.5; color: #35506A;">{{ __('emails.confirmation.role') }}</p>
                        <p style="margin: 10px 0 0; font-size: 14px; color: #35506A;">
                            <a href="mailto:{{ config('site.email') }}" style="color: #004581;">{{ config('site.email') }}</a>
                            @foreach ($socialLinks as $network => $url)
                                &middot; <a href="{{ $url }}" style="color: #004581;">{{ $network }}</a>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <x-slot:footer>
        {!! __('emails.confirmation.footer', ['site' => '<a href="'.e($siteUrl).'" style="color: #97CBDC;">delfinbeta.tech</a>']) !!}
        {{ __('emails.location') }} &middot; #MamaDeveloper
    </x-slot:footer>
</x-mail.layout>
