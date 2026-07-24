<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use App\Models\BlockedDomain;
use App\Models\DmcaDomain;
use Illuminate\Support\Facades\Cache;

final class UrlGuard
{
    private const RESTRICTED_KEYWORDS = [
        'login', 'signin', 'sign-in', 'signup', 'sign-up', 'register', 'registration',
        'lost-password', 'forgot-password', 'reset-password', 'password', 'auth',
        'authentication', 'account', 'profile', 'dashboard', 'admin', 'member',
        'subscription', 'subscribe', 'premium', 'checkout', 'payment', 'billing',
    ];

    public function isRestrictedUrl(string $url): bool
    {
        $urlLower = strtolower($url);

        foreach (self::RESTRICTED_KEYWORDS as $keyword) {
            if (str_contains($urlLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function checkRestricted(string $url): void
    {
        if ($this->isRestrictedUrl($url)) {
            throw new MarretaException(MarretaError::RestrictedUrl);
        }
    }

    public function checkDmca(string $url): void
    {
        $dmcaEntries = Cache::rememberForever('marreta.dmca_domains', function () {
            return DmcaDomain::all()->toArray();
        });

        foreach ($dmcaEntries as $entry) {
            if (isset($entry['host']) && str_contains($url, $entry['host'])) {
                throw new MarretaException(
                    MarretaError::DmcaDomain,
                    $entry['message'] ?? ''
                );
            }
        }
    }

    public function isBlocked(string $host): bool
    {
        $blocked = Cache::rememberForever('marreta.blocked_domains', function () {
            return BlockedDomain::pluck('domain')->toArray();
        });

        if (in_array($host, $blocked, true)) {
            return true;
        }

        // Auto-block: prevent the app from proxying itself
        $selfHost = parse_url(config('marreta.site_url'), PHP_URL_HOST);
        if ($selfHost && $host === $selfHost) {
            return true;
        }

        return false;
    }

    public function checkBlocked(string $host): void
    {
        if ($this->isBlocked($host)) {
            throw new MarretaException(MarretaError::BlockedDomain);
        }
    }
}
