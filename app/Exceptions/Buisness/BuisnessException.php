<?php

namespace App\Exceptions\Buisness;

class BuisnessException extends AbstractBuisnessException
{
    private $userMessage;

    public function __construct(string $userMessage)
    {
        $this->userMessage = $userMessage;
        parent::__construct("Buisness exception");
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
