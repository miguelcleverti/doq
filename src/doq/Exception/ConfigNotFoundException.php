<?php

namespace doq\Exception;

use Exception;

class ConfigNotFoundException extends Exception
{
    public function __construct($configFileName)
    {
        parent::__construct(sprintf("Configuration file '%s' does not exist.", $configFileName));
    }
}
