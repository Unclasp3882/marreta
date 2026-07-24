<?php

declare(strict_types=1);

namespace App\Services\Fetch;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Facades\Log;

final class BrowserFetchStrategy implements FetchStrategy
{
    public function fetch(string $url, array $rules): string
    {
        $wsEndpoint = config('marreta.browser.ws_endpoint');
        $timeoutMs = config('marreta.browser.timeout_ms', 30000);

        if (! $wsEndpoint) {
            throw new MarretaException(MarretaError::HttpError, 'No browser endpoint configured');
        }

        try {
            $browser = BrowserFactory::connectToBrowser($wsEndpoint);

            try {
                $page = $browser->createPage();

                $referrer = 'https://www.google.com/';
                $navigateOptions = ['referrer' => $referrer];

                $page->navigate($url, $navigateOptions)
                    ->waitForNavigation(timeout: $timeoutMs);

                $html = $page->getHtml();

                if (empty($html)) {
                    throw new MarretaException(MarretaError::ContentError);
                }

                return $html;
            } finally {
                $browser->close();
            }
        } catch (MarretaException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Browser fetch failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            $message = $e->getMessage();
            throw new MarretaException(
                match (true) {
                    str_contains($message, 'timeout') => MarretaError::ConnectionError,
                    str_contains($message, 'DNS') => MarretaError::DnsFailure,
                    default => MarretaError::HttpError,
                }
            );
        }
    }
}
