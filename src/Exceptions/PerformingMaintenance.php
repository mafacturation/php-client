<?php

namespace Mafacturation\Exceptions;

use Exception;

class PerformingMaintenance extends Exception
{
    public function __construct($message = 'Mafacturation is performing maintenance. Please try again later.')
    {
        parent::__construct($message);
    }
}