<?php
declare(strict_types=1);

namespace App\Services\Configuration;

use App\Services\Configuration\Interfaces\ConfigHelperInterface;
use InvalidArgumentException;

class ConfigHelper implements ConfigHelperInterface
{
    /**
     * Directory that contains the configuration files
     *
     * @var string
     */
    private $directory;

    /**
     * Array to hold loaded configuration values on a per-file(key) basis
     *
     * @var array
     */
    private $configurations = [];

    /**
     * ConfigHelper constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Get a configuration value or section based on the specified period-separated key.
     *
     * @param   string $key Dot-separated syntax, where first part is always the 'filename'
     *                      parts beyond that represent array keys.
     *
     * @return  mixed
     */
    public function get(string $key)
    {
        /** @var array $configurationParts */
        $configurationParts = \explode('.', $key);

        // If the config key is blank, throw a tantrum
        if ($key === '') {
            throw new \InvalidArgumentException('Configuration key is invalid, or malformed.');
        }

        /**
         * Configuration values based off the first element of the key
         *
         * @var array $value
         */
        $value = $this->getConfigurationFromKey($configurationParts[0] ?? '');


        // Remove first key in array because we've processed it
        \array_shift($configurationParts);

        while (\count($configurationParts) > 0) {
            /** @var string $iterationKey Current key that will be sought from configuration values */
            $iterationKey = $configurationParts[0] ?? '';

            if (\array_key_exists($iterationKey, $value) === false) {
                throw new InvalidArgumentException(
                    \sprintf('Key \'%s\' does not exist within configuration', $iterationKey)
                );
            }

            $value = $value[$iterationKey];

            \array_shift($configurationParts);
        }

        return $value;
    }

    /**
     * Retrieve configuration array based on the key
     * Handles loading of files if required, or using cache
     *
     * @param string $key
     *
     * @return array
     */
    private function getConfigurationFromKey(string $key): array
    {
        $configuration = $this->configurations[$key] ?? null;

        // Return configuration if found in array cache
        if ($configuration !== null) {
            return $configuration;
        }

        $file = \sprintf('%s%s%s.php', $this->directory, DIRECTORY_SEPARATOR, $key);

        if (\is_readable($file) === false) {
            throw new \InvalidArgumentException(
                \sprintf('File \'%s\' does not exist within configuration directory', $key)
            );
        }

        $this->configurations[$key] = require $file;

        return $this->configurations[$key];
    }
}
