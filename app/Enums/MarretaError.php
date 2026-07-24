<?php

declare(strict_types=1);

namespace App\Enums;

enum MarretaError: string
{
    case InvalidUrl = 'INVALID_URL';
    case BlockedDomain = 'BLOCKED_DOMAIN';
    case DmcaDomain = 'DMCA_DOMAIN';
    case NotFound = 'NOT_FOUND';
    case HttpError = 'HTTP_ERROR';
    case ConnectionError = 'CONNECTION_ERROR';
    case DnsFailure = 'DNS_FAILURE';
    case ContentError = 'CONTENT_ERROR';
    case GenericError = 'GENERIC_ERROR';
    case RestrictedUrl = 'RESTRICTED_URL';

    public function httpCode(): int
    {
        return match ($this) {
            self::InvalidUrl => 400,
            self::BlockedDomain, self::DmcaDomain, self::RestrictedUrl => 403,
            self::NotFound => 404,
            self::GenericError => 500,
            self::HttpError, self::ContentError => 502,
            self::ConnectionError => 503,
            self::DnsFailure => 504,
        };
    }
}
