<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $siteName = config('marreta.site_name');
        $siteUrl = config('marreta.site_url');
        $locale = config('marreta.language');

        return response()->json([
            'name' => $siteName,
            'short_name' => $siteName,
            'description' => config('marreta.site_description'),
            'start_url' => $siteUrl,
            'id' => $siteUrl,
            'scope' => '/',
            'display' => 'standalone',
            'display_override' => ['window-controls-overlay', 'minimal-ui'],
            'background_color' => '#ffffff',
            'theme_color' => '#2563eb',
            'orientation' => 'any',
            'lang' => $locale,
            'dir' => 'ltr',
            'prefer_related_applications' => false,
            'icons' => [
                ['src' => 'dist/images/pwa/192x192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => 'dist/images/pwa/512x512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
            'share_target' => [
                'action' => '/p/',
                'method' => 'GET',
                'enctype' => 'application/x-www-form-urlencoded',
                'params' => [
                    'title' => 'title',
                    'text' => 'text',
                    'url' => 'url',
                ],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
