<?php

declare(strict_types=1);

namespace App\Services\Fetch;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use Illuminate\Support\Facades\Http;

final class CurlFetchStrategy implements FetchStrategy
{
    private const GOOGLEBOT_USER_AGENTS = [
        'Googlebot-News',
        'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.6778.200 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/131.0.6778.200 Safari/537.36',
    ];

    public function fetch(string $url, array $rules): string
    {
        $options = [
            'allow_redirects' => ['max' => 2],
            'timeout' => 10,
            'verify' => false,
            'curl' => [
                CURLOPT_DNS_SERVERS => implode(',', config('marreta.dns_servers', ['8.8.8.8'])),
                CURLOPT_ENCODING => '',
            ],
        ];

        if (($rules['proxy'] ?? false) === true && config('marreta.proxy_url')) {
            $options['proxy'] = config('marreta.proxy_url');
        }

        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'DNT' => '1',
        ];

        if (isset($rules['fromGoogleBot'])) {
            $headers['User-Agent'] = $this->randomGooglebotUa();
            $headers['X-Forwarded-For'] = '66.249.'.rand(64, 95).'.'.rand(1, 254);
            $headers['From'] = 'googlebot(at)googlebot.com';
        }

        if (isset($rules['headers']) && is_array($rules['headers'])) {
            $headers = $rules['headers'];
        }

        try {
            $response = Http::withOptions($options)->withHeaders($headers)->get($url);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            throw new MarretaException(
                str_contains($message, 'DNS') ? MarretaError::DnsFailure : MarretaError::ConnectionError
            );
        }

        if ($response->status() === 404) {
            throw new MarretaException(MarretaError::NotFound);
        }

        if ($response->failed() || empty($response->body())) {
            throw new MarretaException(MarretaError::HttpError);
        }

        return $response->body();
    }

    private function randomGooglebotUa(): string
    {
        return self::GOOGLEBOT_USER_AGENTS[array_rand(self::GOOGLEBOT_USER_AGENTS)];
    }
}
