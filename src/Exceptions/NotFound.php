<?php

namespace Mafacturation\MaFacturationPhpClient\Exceptions;

use Exception;

class NotFound extends Exception
{
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message);
    }
}