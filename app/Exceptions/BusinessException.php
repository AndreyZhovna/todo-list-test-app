<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public function __construct(
        private readonly string $userMessage
    ) {
        parent::__construct('Business exception.');
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
