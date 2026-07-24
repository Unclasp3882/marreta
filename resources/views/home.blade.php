<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $site_name }}</title>
    <link rel="icon" href="{{ $site_url }}/dist/icons/marreta.svg" type="image/svg+xml">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="{{ $site_name }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $site_url }}">
    <meta property="og:title" content="{{ $site_name }}">
    <meta property="og:description" content="{{ $site_description }}">
    <meta property="og:image" content="{{ $site_url }}/dist/images/opengraph.png">
    <link rel="stylesheet" href="/dist/css/style.css">
    @livewireStyles
</head>

<body>
    @if($message)
        <div class="toasty toasty--{{ $message_type === 'error' ? 'error' : 'warning' }}">
            <div>
                <p>{{ $message }}</p>
            </div>
            <div>
                <span class="icon icon--close"></span>
            </div>
        </div>
    @endif

    <div class="container">
        <header>
            <div class="open-nav">
                <span class="icon icon--hamburguer"></span>
                <span class="icon icon--close"></span>
            </div>
            <div class="brand">
                <span class="icon icon--marreta"></span>
                <h1>{{ $site_name }}</h1>
            </div>
            <nav>
                <a target="_blank" href="https://github.com/manualdousuario/marreta/wiki/API-Rest">API Rest</a>
                <a target="_blank" href="https://github.com/manualdousuario/marreta/">Github</a>
                <a target="_blank" href="https://github.com/manualdousuario/marreta/blob/main/README.en.md#-integrations">@lang('marreta.nav_integration')</a>
            </nav>
            <div class="fast_buttons">
                <div class="theme-controls">
                    <button class="theme-toggle" id="themeToggle">
                        <span class="icon icon--sun"></span>
                        <span class="icon icon--moon"></span>
                    </button>
                </div>
            </div>
        </header>

        <main>
            <h2 class="description">{{ $site_description }}</h2>

            <form id="urlForm" method="GET" action="/" class="space-y-6">
                <div class="fields">
                    <div class="input">
                        <span class="icon icon--link"></span>
                        <input type="url"
                            name="url"
                            id="url"
                            placeholder="@lang('marreta.url_placeholder')"
                            value="{{ $url }}"
                            required
                            pattern="https?://.+"
                            autofocus>
                        <span class="paste" id="paste"><span class="icon icon--paste"></span></span>
                    </div>
                    <button type="submit" alt="@lang('marreta.analyze_button')">
                        <span class="icon icon--marreta"></span>
                    </button>
                </div>
                <p class="adblock">@lang('marreta.adblocker_warning', ['site_name' => $site_name])</p>
            </form>

            <div class="plus">
                <div class="add_as_app">
                    <h2>
                        <span class="icon icon--android"></span>@lang('marreta.add_as_app')
                    </h2>
                    <div class="text">
                        <div>
                            <ol>
                                <li>@lang('marreta.add_as_app_step1')</li>
                                <li>@lang('marreta.add_as_app_step2')</li>
                                <li>@lang('marreta.add_as_app_step3')</li>
                                <li>@lang('marreta.add_as_app_step4', ['site_name' => $site_name])</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="bookmarklet">
                    <h2>
                        <span class="icon icon--bookmark"></span>@lang('marreta.bookmarklet_title')
                    </h2>
                    <div class="text">
                        <p>
                            {!! __('marreta.bookmarklet_description', ['site_name' => $site_name]) !!}
                        </p>
                        <div>
                            <a href="javascript:(function(){let currentUrl=window.location.href.replace(/^https?:\/\//, '');window.location.href='{{ $site_url }}/p/'+encodeURIComponent(currentUrl);})()"
                                onclick="return false;">
                                {!! __('marreta.open_in', ['site_name' => $site_name]) !!}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @livewireScripts
    <script src="/dist/js/scripts.js"></script>
</body>
</html>
