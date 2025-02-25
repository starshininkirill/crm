<?php

namespace App\Exceptions\Business;
use Exception;

class BusinessException extends Exception
{
    private $userMessage;

    public function __construct(string $userMessage)
    {
        $this->userMessage = $userMessage;
        parent::__construct("Business exception");
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
