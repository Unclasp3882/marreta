<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\MarretaError;
use RuntimeException;

class MarretaException extends RuntimeException
{
    public function __construct(
        private readonly MarretaError $error,
        private readonly string $additionalInfo = '',
        ?\Throwable $previous = null,
    ) {
        $message = $additionalInfo !== '' && $additionalInfo !== '0'
            ? trans("marreta.messages.{$error->value}.message", [], $additionalInfo)
            : trans("marreta.messages.{$error->value}.message");

        parent::__construct($message, $error->httpCode(), $previous);
    }

    public function error(): MarretaError
    {
        return $this->error;
    }

    public function errorType(): string
    {
        return $this->error->value;
    }

    public function additionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
