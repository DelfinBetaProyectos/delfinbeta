<?php

use App\Models\User;
use App\Services\GoogleAnalytics;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('asks for a token with a signed service account assertion and reuses it', function () {
    fakeGoogleAnalytics(fn (array $report): array => [[['20261101'], [3]]]);
    $analytics = new GoogleAnalytics;

    $rows = $analytics->runReport(['dimensions' => [['name' => 'date']], 'metrics' => [['name' => 'sessions']]]);
    $analytics->runReport(['metrics' => [['name' => 'eventCount']]]);

    expect($rows)->toBe([['dimensions' => ['20261101'], 'metrics' => [3.0]]]);

    Http::assertSentCount(3);
    Http::assertSent(function (Request $request): bool {
        if (! str_contains($request->url(), 'oauth2.googleapis.com')) {
            return false;
        }

        [, $claims] = explode('.', $request['assertion']);
        $claims = json_decode(base64_decode(strtr($claims, '-_', '+/')), true);

        return $request['grant_type'] === 'urn:ietf:params:oauth:grant-type:jwt-bearer'
            && $claims['iss'] === 'reader@delfinbeta.iam.gserviceaccount.com'
            && $claims['scope'] === 'https://www.googleapis.com/auth/analytics.readonly';
    });
});

it('is not configured without a property or a readable key', function () {
    config(['services.google_analytics.property_id' => '123', 'services.google_analytics.credentials' => '/missing/key.json']);

    expect((new GoogleAnalytics)->isConfigured())->toBeFalse();

    config(['services.google_analytics.property_id' => null, 'services.google_analytics.credentials' => __FILE__]);

    expect((new GoogleAnalytics)->isConfigured())->toBeFalse();
});

it('loads Google Analytics on public pages for visitors only, with the site language', function () {
    config(['services.google_analytics.measurement_id' => 'G-TEST123']);

    $this->withSession(['locale' => 'es'])
        ->get('/')
        ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123', false)
        ->assertSee("site_locale: 'es'", false);

    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertDontSee('googletagmanager.com', false);
});

it('does not load Google Analytics without a measurement ID', function () {
    config(['services.google_analytics.measurement_id' => null]);

    $this->get('/')->assertDontSee('googletagmanager.com', false);
});
