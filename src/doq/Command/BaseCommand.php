<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command;
use doq\DockerCompose;


class BaseCommand extends Command
{
    protected function getDockerCompose()
    {
        return new DockerCompose();
    }
}
