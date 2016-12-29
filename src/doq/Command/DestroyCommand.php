<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DestroyCommand extends ConfigAwareComposeCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('destroy')
            ->setDescription('Stops and removes containers, networks, volumes, and images');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($result = parent::execute($input, $output) != 0) {
            return $result;
        }

        $output->writeln('<info>Stopping and removing docker containers...</info> ');

        try {
            $this->dockerCompose->execute('down');

            $output->writeln(PHP_EOL . $this->dockerCompose->getOutput(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<info>Done.</info>');
        } catch (\Exception $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed to stop and remove the containers');
            $output->writeln($this->dockerCompose->getOutput());
        }

        return $this->dockerCompose->getResult();
    }
}
