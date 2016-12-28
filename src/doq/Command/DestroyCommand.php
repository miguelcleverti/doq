<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\DockerCompose;
use doq\Exception\ConfigNotFoundException;

class DestroyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('destroy')
            ->setDescription('Stops and removes containers, networks, volumes, and images')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The name of the environment to use',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('<info>Stopping and removing docker containers...</info> ');

        try {
            DockerCompose::executeCommand(
                $input->getOption('config'),
                'down'
            );

            $output->writeln(DockerCompose::lastOutput(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<info>Done.</info>');
        } catch (ConfigNotFoundException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage() );
        } catch (\Exception $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed destroy docker containers:');
            $output->writeln(DockerCompose::lastOutput());
        }
    }
}
