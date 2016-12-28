<?php
namespace doq;

use Exception;
use doq\Exception\ConfigNotFoundException;
use doq\Exception\ConfigExistsException;

class DockerCompose
{
    private static $lastResult;
    private static $lastCommandOutput;


    protected static function getComposeFileName($configName)
    {
        return sprintf('.docker-compose/%s.yml', $configName);
    }

    protected static function getDefaultOpts($configName, $composeFile)
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
     */
    public static function executeCommand($configName, $command, $options=[], $args=[])
    {
        self::assertConfigFileExists($configName);

        // copy source compose file to temporary file in current directory,
        // in order to keep correct working dir (for volumes, etc).
        $sourceFile = DockerCompose::getComposeFileName($configName);
        $tmpComposeFile = tempnam(getcwd(),'compose-');
        if (!copy($sourceFile, $tmpComposeFile)) {
            throw new \Exception('Could not create temporary compose file in current directory.');
        }

        // merge default options for file and project name with provided $options
        $options = array_merge(
            self::getDefaultOpts($configName, $tmpComposeFile),
            $options
        );
        $options = implode(' ', $options);
        $args = implode(' ', $args);

        // escape and execute shell command
        $command = escapeshellcmd("docker-compose $options $command $args 2>&1");
        exec($command, $out, $result);

        // remove temporary file
        @unlink($tmpComposeFile);

        self::$lastCommandOutput =  implode(PHP_EOL, $out);
        self::$lastResult = $result;

        if ($result !== 0) {
            throw new Exception("Command did not finish successfully.");
        }
    }

    public static function lastResult()
    {
        return self::$lastResult;
    }

    public static function lastOutput()
    {
        return PHP_EOL . self::$lastCommandOutput;
    }

    /**
     * Test if a config file exists, throw exception if it does not.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public static function assertConfigFileExists($configName)
    {
        $configFile = DockerCompose::getComposeFileName($configName);

        if (!file_exists($configFile)) {
            throw new ConfigNotFoundException($configFile);
        }
    }

    /**
     * Test if a config file does not exist, throw exception if it does.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigExistsException
     */
    public static function testConfigFileDoesNotExist($configName)
    {
        $configFile = DockerCompose::getComposeFileName($configName);

        if (file_exists($configFile)) {
            throw new ConfigExistsException($configFile);
        }
    }
}
