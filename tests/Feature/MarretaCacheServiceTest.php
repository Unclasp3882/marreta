<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\MarretaCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarretaCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = storage_path('framework/testing/marreta-cache');
        config()->set('marreta.cache.directory', $this->directory);
        config()->set('marreta.cache.disabled', false);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->directory)) {
            array_map(unlink(...), glob($this->directory.DIRECTORY_SEPARATOR.'*') ?: []);
            rmdir($this->directory);
        }

        parent::tearDown();
    }

    public function test_it_counts_the_files_written_to_the_cache_directory(): void
    {
        $cache = app(MarretaCacheService::class);

        $this->assertSame(0, $cache->getCacheFileCount());

        $cache->set('https://example.com/a', '<html>a</html>');
        $cache->set('https://example.com/b', '<html>b</html>');

        $this->assertSame(2, $cache->getCacheFileCount());
    }

    public function test_it_round_trips_cached_content(): void
    {
        $cache = app(MarretaCacheService::class);

        $cache->set('https://example.com/a', '<html>a</html>');

        $this->assertTrue($cache->exists('https://example.com/a'));
        $this->assertSame('<html>a</html>', $cache->get('https://example.com/a'));
    }
}
