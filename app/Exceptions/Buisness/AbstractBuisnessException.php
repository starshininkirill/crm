<?php

namespace App\Exceptions\Buisness;

use Exception;

abstract class AbstractBuisnessException extends Exception
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
