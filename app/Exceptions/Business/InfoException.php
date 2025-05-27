<?php

namespace App\Exceptions\Business;

use Exception;

class InfoException extends Exception
{
    protected $userMessage;

    public function __construct(string $userMessage)
    {
        parent::__construct("Business exception");
        $this->userMessage = $userMessage;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
