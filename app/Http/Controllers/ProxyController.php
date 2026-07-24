<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\MarretaException;
use App\Services\ProxyService;
use App\Services\UrlNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class ProxyController extends Controller
{
    public function __construct(
        private readonly ProxyService $proxyService,
        private readonly UrlNormalizer $urlNormalizer,
    ) {}

    public function __invoke(Request $request, string $url): Response|RedirectResponse
    {
        $processed = $this->urlNormalizer->processRouteUrl($url, $request->query->all());

        if (! $processed['valid']) {
            return redirect('/?message=INVALID_URL');
        }

        if ($processed['needs_redirect']) {
            return redirect(config('marreta.site_url').'/p/'.$processed['url']);
        }

        try {
            $content = $this->proxyService->analyze($processed['url']);

            return response($content, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        } catch (MarretaException $e) {
            if ($e->error()->value === 'DMCA_DOMAIN' && $e->additionalInfo() !== '') {
                return redirect('/?message=DMCA_DOMAIN&info='.urlencode($e->additionalInfo()));
            }

            return redirect('/?message='.$e->errorType());
        }
    }
}
