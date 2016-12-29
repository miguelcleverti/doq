<?php

namespace doq\Exception;

use Exception;

class ConfigExistsException extends Exception
{
    public function __construct($configFileName)
    {
        parent::__construct(sprintf("Configuration file '%s' already exists.", $configFileName));
    }
}
