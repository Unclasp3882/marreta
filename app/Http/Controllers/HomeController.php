<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MarretaCacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(Request $request, MarretaCacheService $cache): View|RedirectResponse
    {
        app()->setLocale(config('marreta.language'));

        $message = '';
        $messageType = '';
        $url = '';

        if ($request->has('message')) {
            $messageKey = trim($request->string('message')->toString());
            $messageData = trans("marreta.messages.{$messageKey}", []);

            if (isset($messageData['message'])) {
                $message = $messageData['message'];
                $messageType = $messageData['type'];
            }
        }

        if ($request->has('url')) {
            $url = trim($request->string('url')->toString());

            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL) !== false) {
                $sanitized = preg_replace('#^https?://#', '', $url);

                return redirect('/p/'.$sanitized);
            }

            if ($url !== '') {
                return redirect('/?message=INVALID_URL');
            }
        }

        return view('home', [
            'message' => $message,
            'message_type' => $messageType,
            'url' => $url,
            'cache_count' => $cache->getCacheFileCount(),
            'site_name' => config('marreta.site_name'),
            'site_description' => config('marreta.site_description'),
            'site_url' => config('marreta.site_url'),
        ]);
    }
}
