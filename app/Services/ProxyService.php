<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use Illuminate\Support\Facades\Log;

final class ProxyService
{
    public function __construct(
        private readonly UrlGuard $urlGuard,
        private readonly RuleEngine $ruleEngine,
        private readonly FetchManager $fetchManager,
        private readonly ContentProcessor $contentProcessor,
        private readonly MarretaCacheService $cache,
    ) {}

    /**
     * Check URL status for redirect detection (used in web mode before analysis).
     *
     * @return array{final_url: string, has_redirect: bool, http_code: int}
     */
    public function checkStatus(string $url): array
    {
        return $this->fetchManager->checkStatus($url);
    }

    /**
     * Analyze a URL: fetch, cache, and process the content.
     *
     * @throws MarretaException
     */
    public function analyze(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            throw new MarretaException(MarretaError::InvalidUrl);
        }

        // Guard: restricted keywords
        $this->urlGuard->checkRestricted($url);

        // Guard: DMCA (checked before any HTTP request)
        $this->urlGuard->checkDmca($url);

        // Cache hit: process the raw content in real-time
        if ($this->cache->exists($url)) {
            $rawContent = $this->cache->get($url);

            return $this->contentProcessor->processContent($rawContent, $host, $url);
        }

        // Guard: blocked domain
        $this->urlGuard->checkBlocked($host);

        // For domains without custom rules, validate HTTP status first
        $rules = $this->ruleEngine->getDomainRules($host);
        $hasCustomRules = $this->ruleEngine->hasDomainRules($host);

        if (! $hasCustomRules) {
            $statusInfo = $this->fetchManager->checkStatus($url);
            if ($statusInfo['http_code'] !== 200) {
                Log::info('URL returned non-200', [
                    'url' => $url,
                    'http_code' => $statusInfo['http_code'],
                ]);

                throw new MarretaException(
                    $statusInfo['http_code'] === 404 ? MarretaError::NotFound : MarretaError::HttpError,
                    (string) $statusInfo['http_code']
                );
            }
        }

        // Fetch using domain strategy or cascade
        $content = $this->fetchManager->fetch($url, $rules);

        // Cache the raw HTML (rules are applied on every request)
        $this->cache->set($url, $content);

        // Process content
        return $this->contentProcessor->processContent($content, $host, $url);
    }
}
