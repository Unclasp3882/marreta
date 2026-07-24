<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlockAiBots
{
    /**
     * User agents associated with AI crawlers and scrapers.
     */
    private const AI_BOT_PATTERNS = [
        'GPTBot', 'ChatGPT-User', 'CCBot', 'Google-Extended', 'OAI-SearchBot',
        'Claude-Web', 'ClaudeBot', 'anthropic-ai', 'Bytespider', 'PerplexityBot',
        'Amazonbot', 'meta-externalagent', 'facebookexternalhit', 'Applebot-Extended',
        'Diffbot', 'ImagesiftBot', 'Omgilibot', 'Omgili', 'YouBot',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent() ?? '';

        foreach (self::AI_BOT_PATTERNS as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                abort(403, 'Access denied for AI crawlers.');
            }
        }

        return $next($request);
    }
}
