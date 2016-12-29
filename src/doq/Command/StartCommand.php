<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends ConfigAwareComposeCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('start')
            ->setDescription('Builds, (re)creates and starts service containers using docker-compose.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($result = parent::execute($input, $output) != 0) {
            return $result;
        }

        $output->writeln('<info>Starting docker service containers...</info> ');

        try {
            $this->dockerCompose->execute('up -d');

            $output->writeln(PHP_EOL . $this->dockerCompose->getOutput(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<info>Done.</info>');
        } catch (\Exception $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed to bring up the containers using docker-compose');
            $output->writeln($this->dockerCompose->getOutput());
        }

        return $this->dockerCompose->getResult();
    }
}
