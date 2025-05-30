<?php

namespace App\Exceptions\Api;

use Exception;

class ApiException extends Exception
{
    protected $userMessage;
    protected $statusCode;

    public function __construct($statusCode, string $userMessage)
    {
        parent::__construct("ApiException");
        $this->userMessage = $userMessage;
        $this->statusCode = $statusCode;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }
}
