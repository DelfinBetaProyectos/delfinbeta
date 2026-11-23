<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal Google Analytics 4 Data API client, authenticated with a service account key.
 *
 * Reports are cached for a few minutes so the dashboard does not hit the API on every change.
 */
class GoogleAnalytics
{
    private const SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const REPORT_CACHE_SECONDS = 600;

    /**
     * Whether a property and a readable service account key are configured.
     */
    public function isConfigured(): bool
    {
        $credentials = config('services.google_analytics.credentials');

        return filled(config('services.google_analytics.property_id'))
            && filled($credentials)
            && is_readable($credentials);
    }

    /**
     * Run a report (the body of the Data API `runReport` method) and return its rows.
     *
     * @param  array<string, mixed>  $request
     * @return list<array{dimensions: list<string>, metrics: list<float>}>
     *
     * @throws RuntimeException When the API is not configured or the request fails.
     */
    public function runReport(array $request): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Google Analytics is not configured.');
        }

        $propertyId = config('services.google_analytics.property_id');

        return Cache::remember('google-analytics.report.'.md5($propertyId.json_encode($request)), self::REPORT_CACHE_SECONDS, function () use ($propertyId, $request): array {
            $response = Http::withToken($this->accessToken())
                ->acceptJson()
                ->timeout(15)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", $request)
                ->throw();

            return array_values(array_map(fn (array $row): array => [
                'dimensions' => array_values(array_map(fn (array $value): string => (string) ($value['value'] ?? ''), $row['dimensionValues'] ?? [])),
                'metrics' => array_values(array_map(fn (array $value): float => (float) ($value['value'] ?? 0), $row['metricValues'] ?? [])),
            ], $response->json('rows') ?? []));
        });
    }

    /**
     * An OAuth access token for the service account, cached until shortly before it expires.
     */
    private function accessToken(): string
    {
        $cached = Cache::get('google-analytics.token');

        if (is_string($cached)) {
            return $cached;
        }

        $response = Http::asForm()->timeout(15)->post(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->signedAssertion(),
        ])->throw();

        $token = (string) $response->json('access_token');
        Cache::put('google-analytics.token', $token, max(60, (int) $response->json('expires_in', 3600) - 300));

        return $token;
    }

    /**
     * The JWT (RS256) the service account signs to ask for a token.
     */
    private function signedAssertion(): string
    {
        $credentials = json_decode((string) file_get_contents(config('services.google_analytics.credentials')), true);

        if (! is_array($credentials) || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            throw new RuntimeException('The Google Analytics service account key is not valid.');
        }

        $now = time();
        $segments = [
            $this->base64UrlEncode((string) json_encode(['alg' => 'RS256', 'typ' => 'JWT'])),
            $this->base64UrlEncode((string) json_encode([
                'iss' => $credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URL,
                'iat' => $now,
                'exp' => $now + 3600,
            ])),
        ];

        if (! openssl_sign(implode('.', $segments), $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Could not sign the Google Analytics token request.');
        }

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /**
     * Base64 without padding, as JWT requires.
     */
    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
