<?php
namespace doq;

use Symfony\Component\Console\Application as BaseApp;
use doq\Command;

/**
 * Implement Symfony Console application
 */
class Application extends BaseApp
{
    const NAME = 'doq - docker-compose service configuration manager';
    const VERSION = '1.0';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->add(new Command\InitCommand());
        $this->add(new Command\StartCommand());
        $this->add(new Command\DestroyCommand());
    }
}
