<?php

declare(strict_types=1);

namespace App\Services\Fetch;

interface FetchStrategy
{
    /**
     * Fetch content from a URL.
     *
     * @param  array<string, mixed>  $rules  Merged domain rules.
     *
     * @throws \App\Exceptions\MarretaException
     */
    public function fetch(string $url, array $rules): string;
}
