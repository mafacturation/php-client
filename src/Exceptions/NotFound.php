<?php

namespace Mafacturation\PhpClient\Exceptions;

use Exception;

class NotFound extends Exception
{
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message);
    }
}