<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\MarretaException;
use App\Http\Controllers\Controller;
use App\Services\ProxyService;
use App\Services\UrlNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ProxyController extends Controller
{
    public function __construct(
        private readonly ProxyService $proxyService,
        private readonly UrlNormalizer $urlNormalizer,
    ) {}

    public function __invoke(Request $request, string $url): JsonResponse|RedirectResponse
    {
        $processed = $this->urlNormalizer->processRouteUrl($url, $request->query->all());

        if (! $processed['valid']) {
            return redirect('/?message=INVALID_URL');
        }

        if ($processed['needs_redirect']) {
            return redirect(config('marreta.site_url').'/api/'.$processed['url']);
        }

        try {
            $this->proxyService->analyze($processed['url']);

            $displayUrl = preg_replace('#^https?://#', '', $processed['url']);

            return response()->json([
                'status' => 200,
                'url' => config('marreta.site_url').'/p/'.$displayUrl,
            ], 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
            ]);
        } catch (MarretaException $e) {
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'X-Error-Type' => $e->errorType(),
            ];

            if ($e->additionalInfo()) {
                $headers['X-Error-Info'] = $e->additionalInfo();
            }

            return response()->json([
                'status' => $e->error()->httpCode(),
                'error' => [
                    'type' => $e->errorType(),
                    'message' => $e->getMessage(),
                    'details' => $e->additionalInfo() ?: null,
                ],
            ], $e->error()->httpCode(), $headers);
        }
    }
}
