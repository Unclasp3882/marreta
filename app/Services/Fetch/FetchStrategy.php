<?php

declare(strict_types=1);

namespace App\Services\Fetch;

use App\Exceptions\MarretaException;

interface FetchStrategy
{
    /**
     * Fetch content from a URL.
     *
     * @param  array<string, mixed>  $rules  Merged domain rules.
     *
     * @throws MarretaException
     */
    public function fetch(string $url, array $rules): string;
}
