@props([
    'preheader',
    'tagline' => null,
])

{{-- Shared shell for the contact emails: table layout and inline styles so mail clients render it. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $preheader }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@500&amp;family=Inter:wght@400;500&amp;family=Space+Grotesk:wght@700&amp;display=swap">
    <style>
        a { color: #004581; }
        @media (max-width: 620px) {
            .mail-pad { padding-left: 24px !important; padding-right: 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background: #DDE8F0;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0;">{{ $preheader }}</div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; background: #DDE8F0;">
        <tr>
            <td align="center" style="padding: 32px 12px 48px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="width: 100%; max-width: 600px; border-collapse: separate; background: #FFFFFF; border-radius: 12px; overflow: hidden; font-family: Inter, Arial, Helvetica, sans-serif; color: #001B48;">
                    <tr>
                        <td class="mail-pad" style="background: #001B48; padding: {{ $tagline ? '22px' : '28px' }} 40px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td>
                                        {{-- Same brand as the dashboard sidebar: white isotipo + logotipo, linked to the site --}}
                                        <a href="{{ route('home') }}" style="text-decoration: none;">
                                            <img src="{{ asset('images/brand/DelfinBeta_Isotipo_White.png') }}" alt="" width="40" style="display: inline-block; width: 40px; height: auto; border: 0; vertical-align: middle;">
                                            <img src="{{ asset('images/brand/DelfinBeta_Logotipo_White.png') }}" alt="{{ config('app.name') }}" width="120" height="32" style="display: inline-block; width: 120px; height: 32px; margin-left: 8px; border: 0; vertical-align: middle; font-family: 'Space Grotesk', Arial, Helvetica, sans-serif; font-weight: 700; font-size: 20px; color: #FFFFFF;">
                                        </a>
                                    </td>
                                    @if ($tagline)
                                        <td align="right" style="font-family: 'Fira Code', Consolas, Menlo, monospace; font-size: 12px; color: #A9C3D6;">{{ $tagline }}</td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td style="background: #018ABD; height: 4px; line-height: 4px; font-size: 0;">&nbsp;</td></tr>

                    {{ $slot }}

                    <tr>
                        <td class="mail-pad" style="background: #001B48; padding: 20px 40px; font-size: 12px; line-height: 1.6; color: #A9C3D6;">{{ $footer }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
