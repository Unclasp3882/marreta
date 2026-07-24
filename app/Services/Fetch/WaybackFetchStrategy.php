<?php

declare(strict_types=1);

namespace App\Services\Fetch;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use Illuminate\Support\Facades\Http;

final class WaybackFetchStrategy implements FetchStrategy
{
    public function fetch(string $url, array $rules): string
    {
        $proxy = (($rules['proxy'] ?? false) === true) ? config('marreta.proxy_url') : null;

        $options = [
            'allow_redirects' => true,
            'timeout' => 10,
            'verify' => false,
        ];
        if ($proxy) {
            $options['proxy'] = $proxy;
        }

        $lookupUrl = preg_replace('#^https?://#', '', $url);
        $availabilityUrl = 'https://archive.org/wayback/available?url='.urlencode($lookupUrl);

        try {
            $response = Http::withOptions($options)
                ->withUserAgent($this->randomUserAgent())
                ->get($availabilityUrl);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            throw new MarretaException(
                str_contains($message, 'DNS') ? MarretaError::DnsFailure : MarretaError::ConnectionError
            );
        }

        if ($response->failed()) {
            throw new MarretaException(MarretaError::HttpError);
        }

        $data = $response->json();
        if (! isset($data['archived_snapshots']['closest']['url'])) {
            throw new MarretaException(MarretaError::NotFound);
        }

        $archiveUrl = $data['archived_snapshots']['closest']['url'];

        try {
            $archiveResponse = Http::withOptions($options)
                ->withUserAgent($this->randomUserAgent())
                ->get($archiveUrl);
        } catch (\Throwable $e) {
            throw new MarretaException(MarretaError::ConnectionError);
        }

        if ($archiveResponse->failed() || empty($archiveResponse->body())) {
            throw new MarretaException(MarretaError::HttpError);
        }

        $content = $archiveResponse->body();

        // Strip Wayback toolbar
        $content = preg_replace(
            '/<!-- BEGIN WAYBACK TOOLBAR INSERT -->.*?<!-- END WAYBACK TOOLBAR INSERT -->/s',
            '',
            $content
        );
        $content = preg_replace(
            '#https?://web\.archive\.org/web/\d+im_/#',
            '',
            $content
        );

        return $content;
    }

    private function randomUserAgent(): string
    {
        $agents = [
            'Googlebot-News',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ];

        return $agents[array_rand($agents)];
    }
}
