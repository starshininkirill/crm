<?php

namespace App\Exceptions\Business;

use Exception;

class BusinessException extends Exception
{
    private $userMessage;
    private $isFlash;

    public function __construct(string $userMessage, bool $isFlash = false)
    {
        $this->userMessage = $userMessage;
        $this->isFlash = $isFlash;
        parent::__construct("Business exception");
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function isFlash()
    {
        return $this->isFlash;
    }
}
