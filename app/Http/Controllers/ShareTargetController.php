<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ShareTargetController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $url = trim($request->input('url', '') ?? '');
        $text = trim($request->input('text', '') ?? '');

        $candidate = $url !== '' ? $url : $text;

        if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_URL) !== false) {
            $sanitized = preg_replace('#^https?://#', '', $candidate);

            return redirect('/p/'.$sanitized);
        }

        return redirect('/?message=INVALID_URL');
    }
}
