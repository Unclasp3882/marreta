<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\MarretaError;
use App\Exceptions\MarretaException;
use App\Http\Controllers\Controller;
use App\Services\ProxyService;
use App\Services\UrlNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProxyController extends Controller
{
    public function __construct(
        private readonly ProxyService $proxyService,
        private readonly UrlNormalizer $urlNormalizer,
    ) {}

    public function __invoke(Request $request, string $url): JsonResponse
    {
        $headers = ['Access-Control-Allow-Origin' => '*'];

        try {
            $processed = $this->urlNormalizer->processRouteUrl($url, $request->query->all());

            if (! $processed['valid']) {
                throw new MarretaException(MarretaError::InvalidUrl);
            }

            $this->proxyService->analyze($processed['url']);

            $displayUrl = preg_replace('#^https?://#', '', $processed['url']);

            return response()->json([
                'status' => 200,
                'url' => config('marreta.site_url').'/p/'.$displayUrl,
            ], 200, $headers + ['Access-Control-Allow-Methods' => 'GET']);
        } catch (MarretaException $e) {
            $headers['X-Error-Type'] = $e->errorType();

            if ($e->additionalInfo()) {
                $headers['X-Error-Info'] = $e->additionalInfo();
            }

            return response()->json([
                'status' => $e->error()->httpCode(),
                'error' => [
                    'code' => $e->errorType(),
                    'message' => $e->getMessage(),
                    'details' => $e->additionalInfo() ?: null,
                ],
            ], $e->error()->httpCode(), $headers);
        }
    }
}
