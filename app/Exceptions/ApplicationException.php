<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApplicationException extends Exception
{
    public function __construct(string $message = "Application error", int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $code);
    }
}
