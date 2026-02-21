<?php

namespace App\Exceptions;

use RuntimeException;

class SpeechAnalysisException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $httpStatus,
        public readonly string $responseBody,
    ) {
        parent::__construct($message);
    }
}
