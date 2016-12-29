<?php
namespace doq;

use Exception;
use doq\Exception\ConfigNotFoundException;
use doq\Exception\ConfigExistsException;

class DockerCompose
{
    const COMPOSE_FOLDER = '.docker-compose';

    protected $configName;
    protected $configFile;

    protected $lastResult;
    protected $lastCommandOutput;


    public function useConfiguration($configName)
    {
        $this->configName = $configName;
        $this->configFile = $this->getComposeFileName($configName);
    }

    /**
     * Test if a config file exists, throw exception if it does not.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public function assertConfigFileExists()
    {
        if (!$this->configFile || !file_exists($this->configFile)) {
            throw new ConfigNotFoundException($this->configFile);
        }
    }

    /**
     * Test if a config file does not exist, throw exception if it does.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigExistsException
     */
    public function testConfigFileDoesNotExist($configName)
    {
        $configFile = $this->getComposeFileName($configName);

        if (file_exists($configFile)) {
            throw new ConfigExistsException($configFile);
        }
    }

    /**
     * Execute a command in docker-compose, using a configuration file and name.
     *
     * @param string $configName the name of the configuration to use.
     * @param string $command the command to execute with docker-compose
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public function executeCommand($command, $options=[], $args=[])
    {
        $this->assertConfigFileExists();

        // copy source compose file to temporary file in current directory,
        // in order to keep correct working dir (for volumes, etc).
        $tmpComposeFile = tempnam(getcwd(),'compose-');
        if (!copy($this->configFile, $tmpComposeFile)) {
            throw new Exception('Could not create temporary compose file in current directory.');
        }

        // merge default options for file and project name with provided $options
        $options = array_merge(
            $this->getDefaultOpts($this->configName, $tmpComposeFile),
            $options
        );

        $this->exec($command, $options, $args);

        @unlink($this->tmpComposeFile);

        if ($this->lastResult() !== 0) {
            throw new Exception("Command did not finish successfully.");
        }
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

        //exec($command, $out, $result);

        $this->lastCommandOutput = implode(PHP_EOL, $out);
        $this->lastResult = $result;
    }

    public function lastResult()
    {
        return $this->lastResult;
    }

    public function lastOutput()
    {
        return $this->lastCommandOutput;
    }

    protected function getComposeFileName($configName)
    {
        return sprintf('%s/%s.yml', self::COMPOSE_FOLDER, $configName);
    }

    protected function getDefaultOpts($configName, $composeFile)
    {
        $currentDirName = dirname(getcwd());
        $projectName = sprintf('%s.%s', $currentDirName, $configName);

        return ["--project-name $projectName", "--file $composeFile"];
    }
}
