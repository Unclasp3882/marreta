<?php

declare(strict_types=1);

namespace App\Services;

final class UrlNormalizer
{
    /**
     * Process a raw URL from the route, normalizing scheme, encoding and slashes.
     *
     * @return array{valid: bool, url: string, needs_redirect: bool}
     */
    public function processRouteUrl(string $rawUrl, array $queryParams = []): array
    {
        $queryString = '';
        $cleanParams = [];
        foreach ($queryParams as $key => $value) {
            if ($key !== 'url' && $key !== 'text') {
                $cleanParams[$key] = $value;
            }
        }
        if ($cleanParams !== []) {
            $queryString = '?'.http_build_query($cleanParams);
        }

        $url = $rawUrl;
        $hasScheme = (bool) preg_match('#^https?://#', $url);

        if ($hasScheme) {
            $url = preg_replace('#^https?://#', '', $url);
        }

        $needsDecoding = str_contains($url, '%') && (bool) preg_match('/%[0-9A-Fa-f]{2}/', $url);
        if ($needsDecoding) {
            $url = urldecode($url);
        }

        $needsRedirect = $hasScheme || $rawUrl !== $url;

        $url = preg_replace('#/+#', '/', $url);
        $url = 'https://'.$url;

        if (! $this->isValidUrl($url)) {
            return ['valid' => false, 'url' => '', 'needs_redirect' => false];
        }

        $sanitized = $this->sanitize($url);
        if ($sanitized === '') {
            return ['valid' => false, 'url' => '', 'needs_redirect' => false];
        }

        $sanitized .= $queryString;

        return [
            'valid' => true,
            'url' => $sanitized,
            'needs_redirect' => $needsRedirect,
        ];
    }

    /**
     * Sanitize and normalize a full URL.
     */
    public function sanitize(string $url): string
    {
        $url = trim($url);

        if (! $this->isValidUrl($url)) {
            return '';
        }

        // Handle AMP CDN URLs — extract the original URL
        if (preg_match('#https://([^.]+)\.cdn\.ampproject\.org/v/s/([^/]+)(.*)#', $url, $matches)) {
            $url = 'https://'.$matches[2].$matches[3];
        }

        $parts = parse_url($url);
        if (! isset($parts['scheme']) || ! isset($parts['host'])) {
            return '';
        }

        $cleaned = $parts['host'];
        $cleaned .= $parts['path'] ?? '';
        $cleaned .= isset($parts['query']) ? '?'.$parts['query'] : '';
        $cleaned .= isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return preg_replace('/[\x00-\x1F\x7F]/', '', $cleaned);
    }

    /**
     * Validate a URL, tolerating non-ASCII characters that FILTER_VALIDATE_URL rejects
     * (e.g. accented letters in Wikipedia-style paths) by percent-encoding them first.
     */
    private function isValidUrl(string $url): bool
    {
        $ascii = preg_replace_callback(
            '/[^\x21-\x7E]/',
            fn (array $m): string => rawurlencode($m[0]),
            $url,
        );

        return filter_var($ascii, FILTER_VALIDATE_URL) !== false;
    }
}
