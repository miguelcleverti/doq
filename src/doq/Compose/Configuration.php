<?php

namespace doq\Compose;

use Exception;
use doq\Exception\ConfigNotFoundException;
use doq\Exception\ConfigExistsException;

class Configuration
{
    const COMPOSE_FOLDER = '.docker-compose';

    protected $configName;
    protected $configFile;

    public function __construct($configName)
    {
        $this->configName = $configName;
        $this->configFile = $this->getConfigFilePath();
    }

    /**
     * Takes a configuration name and returns the local path to the file.
     */
    protected function getConfigFilePath()
    {
        return sprintf('%s/%s.yml', self::COMPOSE_FOLDER, $this->configName);
    }

    public function getName()
    {
        return $this->configName;
    }

    public function getFile()
    {
        return $this->configFile;
    }

    /**
     * Copy source compose config file to temporary file in current directory.
     *
     * @return string the temporary file name
     */
    public function copyTempFile()
    {
        $this->assertFileExists();

        $tmpConfigFile = tempnam(getcwd(), 'compose-');
        if (!copy($this->getFile(), $tmpConfigFile)) {
            throw new Exception('Could not create temporary compose file in current directory.');
        }
        return $tmpConfigFile;
    }

    /**
     * Test if a config file exists, throw exception if it does not.
     *
     * @param $configName
     *
     * @throws doq\Exception\ConfigNotFoundException
     */
    public function assertFileExists()
    {
        if (!file_exists($this->configFile)) {
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
    public function assertFileDoesNotExist()
    {
        if (file_exists($this->configFile)) {
            throw new ConfigExistsException($this->configFile);
        }
    }
}
