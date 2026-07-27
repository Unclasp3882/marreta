<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use App\Services\Fetch\BrowserFetchStrategy;
use App\Services\Fetch\CurlFetchStrategy;
use App\Services\Fetch\FetchStrategy;
use App\Services\Fetch\WaybackFetchStrategy;
use Illuminate\Support\Facades\Http;

final class FetchManager
{
    /**
     * @param  array<string, FetchStrategy>  $strategies
     */
    public function __construct(
        private readonly CurlFetchStrategy $curlFetch,
        private readonly WaybackFetchStrategy $waybackFetch,
        private readonly BrowserFetchStrategy $browserFetch,
    ) {}

    /**
     * Fetch content using domain-specific strategy or cascade.
     *
     * @param  array<string, mixed>  $rules
     */
    public function fetch(string $url, array $rules): string
    {
        $url = $this->applyUrlMods($url, $rules);

        $strategies = $this->strategyMap();

        // Use domain-specific fetchStrategies if set (falls through to the normal cascade
        // when it points at the browser but no browser endpoint is configured)
        $strategyName = $rules['fetchStrategies'] ?? null;
        if ($strategyName && isset($strategies[$strategyName]) && $this->strategyAvailable($strategyName)) {
            return $strategies[$strategyName]->fetch($url, $rules);
        }

        // Cascade: cURL → Wayback → Browser
        $cascade = array_filter(
            ['fetchContent', 'fetchFromWaybackMachine', 'fetchFromSelenium'],
            fn (string $name): bool => $this->strategyAvailable($name),
        );
        $lastError = null;

        foreach ($cascade as $name) {
            try {
                $content = $strategies[$name]->fetch($url, $rules);
                if (! empty($content)) {
                    return $content;
                }
            } catch (MarretaException $e) {
                $lastError = $e;

                continue;
            }
        }

        throw $lastError ?? new MarretaException(MarretaError::ContentError);
    }

    /**
     * Check URL status and redirect chain via HEAD request.
     *
     * @return array{final_url: string, has_redirect: bool, http_code: int}
     */
    public function checkStatus(string $url): array
    {
        $options = [
            'allow_redirects' => true,
            'timeout' => 5,
            'verify' => false,
            'curl' => [
                CURLOPT_NOBODY => true,
            ],
        ];

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        ];

        try {
            $response = Http::withOptions($options)->withHeaders($headers)->head($url);
            $finalUrl = $response->effectiveUri() ?: $url;
            $httpCode = $response->status();
        } catch (\Throwable) {
            return ['final_url' => $url, 'has_redirect' => false, 'http_code' => 0];
        }

        return [
            'final_url' => (string) $finalUrl,
            'has_redirect' => $finalUrl !== $url,
            'http_code' => $httpCode,
        ];
    }

    /**
     * @return array<string, FetchStrategy>
     */
    private function strategyMap(): array
    {
        return [
            'fetchContent' => $this->curlFetch,
            'fetchFromWaybackMachine' => $this->waybackFetch,
            'fetchFromSelenium' => $this->browserFetch,
        ];
    }

    /**
     * The browser strategy requires BROWSER_WS_ENDPOINT to be configured.
     */
    private function strategyAvailable(string $name): bool
    {
        return $name !== 'fetchFromSelenium' || config('marreta.browser.ws_endpoint') !== null;
    }

    /**
     * Apply urlMods from domain rules to modify query parameters.
     */
    private function applyUrlMods(string $url, array $rules): string
    {
        if (! isset($rules['urlMods']['query']) || ! is_array($rules['urlMods']['query'])) {
            return $url;
        }

        $parts = parse_url($url);
        $queryParams = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParams);
        }

        foreach ($rules['urlMods']['query'] as $mod) {
            if (isset($mod['key']) && isset($mod['value'])) {
                $queryParams[$mod['key']] = $mod['value'];
            }
        }

        $parts['query'] = http_build_query($queryParams);

        $result = ($parts['scheme'] ?? 'https').'://';
        $result .= $parts['host'] ?? '';
        $result .= isset($parts['port']) ? ':'.$parts['port'] : '';
        $result .= $parts['path'] ?? '';
        $result .= isset($parts['query']) ? '?'.$parts['query'] : '';
        $result .= isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $result;
    }
}
