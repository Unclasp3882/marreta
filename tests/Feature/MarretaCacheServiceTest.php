<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\MarretaCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->directory = storage_path('framework/testing/marreta-cache');

    config()->set('marreta.cache.directory', $this->directory);
    config()->set('marreta.cache.disabled', false);
});

afterEach(function () {
    if (is_dir($this->directory)) {
        array_map(unlink(...), glob($this->directory.DIRECTORY_SEPARATOR.'*') ?: []);
        rmdir($this->directory);
    }
});

it('counts the files written to the cache directory', function () {
    $cache = app(MarretaCacheService::class);

    expect($cache->getCacheFileCount())->toBe(0);

    $cache->set('https://example.com/a', '<html>a</html>');
    $cache->set('https://example.com/b', '<html>b</html>');

    expect($cache->getCacheFileCount())->toBe(2);
});

it('round trips cached content', function () {
    $cache = app(MarretaCacheService::class);

    $cache->set('https://example.com/a', '<html>a</html>');

    expect($cache->exists('https://example.com/a'))->toBeTrue()
        ->and($cache->get('https://example.com/a'))->toBe('<html>a</html>');
});
