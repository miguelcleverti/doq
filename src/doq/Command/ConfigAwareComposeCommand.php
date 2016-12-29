<?php

namespace doq\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use doq\Command\ComposeCommand;
use doq\Compose\Configuration;
use doq\Exception\ConfigNotFoundException;

class ConfigAwareComposeCommand extends ComposeCommand
{
    protected $dockerCompose;

    protected function configure()
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'The name of the environment to use',
            'default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ( $result = parent::execute($input, $output) != 0 ) {
            return $result;
        }

        $output->write('<info>Loading configuration...</info> ', OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->configuration = new Configuration($input->getOption('config'));

        try {
            $this->dockerCompose->setConfiguration($this->configuration);
        } catch (ConfigNotFoundException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage() );
            return 1;
        }
    }

}
