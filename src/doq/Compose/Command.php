<?php

namespace doq\Compose;

use doq\Compose\Configuration;
use doq\Exception\ConfigNotFoundException;
use doq\Exception\ConfigExistsException;

class Command
{
    /**
     * @var doq\Compose\Configuration
     */
    protected $config;

    /**
     * Result status of last compose shell execution
     * @var int
     */
    protected $lastResult;

    /**
     * Result output of last compose shell execution
     * @var string
     */
    protected $lastCommandOutput;

    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;
    }

    protected function getDefaultOpts($configName, $composeFile)
    {
        $currentDirName = dirname(getcwd());
        $projectName = sprintf('%s.%s', $currentDirName, $configName);

        return ["--project-name $projectName", "--file $composeFile"];
    }

    /**
     * Execute a command in docker-compose, using a configuration file and name.
     *
     * @param string $configName the name of the configuration to use.
     * @param string $command the command to execute with docker-compose
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public function execute($command, $options = [], $args = [])
    {
        $tmpConfigFile = $this->config->copyTempFile();

        // merge default options for file and project name with provided $options
        $options = array_merge(
            $this->getDefaultOpts($this->config->getName(), $tmpConfigFile),
            $options
        );

        $this->exec($command, $options, $args);

        unlink($tmpConfigFile);

        if ($this->getResult() !== 0) {
            throw new Exception("Command did not finish successfully.");
        }
    }

    public function getResult()
    {
        return $this->lastResult;
    }

    public function getOutput()
    {
        return $this->lastCommandOutput;
    }

    /**
     * Execute shell command and store result/output.
     */
    protected function exec($command, $options, $args)
    {
        $options = implode(' ', $options);
        $args = implode(' ', $args);

        // escape and execute shell command
        $command = escapeshellcmd("docker-compose $options $command $args");

        exec($command, $out, $result);

        $this->lastCommandOutput = implode(PHP_EOL, $out);
        $this->lastResult = $result;
    }
}
