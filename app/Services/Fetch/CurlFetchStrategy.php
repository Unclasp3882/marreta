<?php

declare(strict_types=1);

namespace App\Services\Fetch;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use Illuminate\Support\Facades\Http;

final class CurlFetchStrategy implements FetchStrategy
{
    public function fetch(string $url, array $rules): string
    {
        $options = [
            'allow_redirects' => ['max' => 2],
            'timeout' => 10,
            'verify' => false,
            'curl' => [
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
}
