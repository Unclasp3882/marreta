<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Stat;
use Illuminate\Filesystem\Filesystem;

final class MarretaCacheService
{
    public function __construct(
        private readonly Filesystem $files,
    ) {}

    public function generateId(string $url): string
    {
        $normalized = preg_replace('#^https?://(www\.)?#', '', $url);

        return hash('sha256', $normalized);
    }

    public function exists(string $url): bool
    {
        if (config('marreta.cache.disabled')) {
            return false;
        }

        return $this->files->exists($this->path($this->generateId($url)));
    }

    public function get(string $url): ?string
    {
        if (config('marreta.cache.disabled')) {
            return null;
        }

        $path = $this->path($this->generateId($url));
        if (! $this->files->exists($path)) {
            return null;
        }

        $compressed = $this->files->get($path);

        $decoded = gzdecode($compressed);

        return $decoded === false ? null : $decoded;
    }

    public function set(string $url, string $content): bool
    {
        if (config('marreta.cache.disabled')) {
            return true;
        }

        $this->ensureDirectory();
        $level = config('marreta.cache.compress_level', 3);
        $compressed = gzencode($content, $level);

        if ($compressed === false) {
            return false;
        }

        $this->files->put($this->path($this->generateId($url)), $compressed);

        Stat::incrementCounter('cache_count');

        return true;
    }

    public function getCacheFileCount(): int
    {
        $this->ensureDirectory();

        $count = count($this->files->glob($this->path('*')) ?: []);
        Stat::updateOrCreate(['key' => 'cache_count'], ['value' => $count]);

        return $count;
    }

    private function path(string $id): string
    {
        return config('marreta.cache.directory').DIRECTORY_SEPARATOR.$id.'.gz';
    }

    private function ensureDirectory(): void
    {
        $dir = config('marreta.cache.directory');
        if (! $this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0775, true);
        }
    }
}
