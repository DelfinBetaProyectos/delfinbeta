<?php

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Point the GA4 config to a throwaway service account and answer the Data API with `$rowsFor`,
 * which receives each runReport body and returns its rows as [[dimension values], [metric values]]
 * (or a faked response, e.g. an error).
 *
 * @param  callable(array<string, mixed>): (list<array{0: list<string>, 1: list<int|float>}>|PromiseInterface)  $rowsFor
 */
function fakeGoogleAnalytics(callable $rowsFor): void
{
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($key, $privateKey);

    $credentials = tempnam(sys_get_temp_dir(), 'ga4');
    file_put_contents($credentials, json_encode(['client_email' => 'reader@delfinbeta.iam.gserviceaccount.com', 'private_key' => $privateKey]));

    config([
        'services.google_analytics.property_id' => '123456',
        'services.google_analytics.credentials' => $credentials,
    ]);

    Http::fake([
        'oauth2.googleapis.com/*' => Http::response(['access_token' => 'ga-token', 'expires_in' => 3600]),
        'analyticsdata.googleapis.com/*' => function (Request $request) use ($rowsFor) {
            $rows = $rowsFor($request->data());

            return $rows instanceof PromiseInterface ? $rows : Http::response([
                'rows' => array_map(fn (array $row): array => [
                    'dimensionValues' => array_map(fn ($value): array => ['value' => (string) $value], $row[0]),
                    'metricValues' => array_map(fn ($value): array => ['value' => (string) $value], $row[1]),
                ], $rows),
            ]);
        },
    ]);
}

/**
 * Names of the dimensions (or metrics) of a GA4 runReport body, e.g. ['pagePath'].
 *
 * @param  array<string, mixed>  $report
 * @return list<string>
 */
function reportFields(array $report, string $type = 'dimensions'): array
{
    return array_column($report[$type] ?? [], 'name');
}
