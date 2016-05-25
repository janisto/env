<?php

namespace janisto\env;

/**
 * Environment class, used to set configuration depending on the server environment.
 *
 * @author Jani Mikkonen <janisto@php.net>
 * @license public domain (http://unlicense.org)
 * @link https://github.com/janisto/env
 */
class Environment
{
    /**
     * Environment variable. Use Apache SetEnv or export in shell.
     *
     * @var string environment variable name
     */
    protected $envName = 'APP_ENV';
    /**
     * @var array list of valid modes
     */
    protected $validModes = [
        'dev',
        'test',
        'stage',
        'prod',
    ];
    /**
     * @var array configuration directory(s)
     */
    protected $configDir = [];
    /**
     * @var string selected environment mode
     */
    protected $mode;
    /**
     * @var array application configuration array
     */
    public $config = [];

    /**
     * Constructor. Initializes the Environment class.
     *
     * @param string|array $configDir configuration directory(s)
     * @param string $mode override automatically set environment mode
     * @throws \Exception
     */
    public function __construct($configDir, $mode = null)
    {
        $this->setConfigDir($configDir);
        $this->setMode($mode);
        $this->setEnvironment();
    }

    /**
     * Set configuration directory(s) where the config files are stored.
     *
     * @param string|array $configDir configuration directory(s)
     * @throws \Exception
     */
    protected function setConfigDir($configDir)
    {
        $this->configDir = [];
        foreach ((array) $configDir as $k => $v) {
            $dir = rtrim($v, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if (!is_dir($dir)) {
                throw new \Exception('Invalid configuration directory "' . $dir . '".');
            }
            $this->configDir[$k] = $dir;
        }
    }

    /**
     * Set environment mode.
     *
     * @param string|null $mode environment mode
     * @throws \Exception
     */
    protected function setMode($mode)
    {
        if ($mode === null) {
            // Return mode based on environment variable.
            $mode = getenv($this->envName);
            if ($mode === false) {
                // Defaults to production.
                $mode = 'prod';
            }
        }

        // Check if mode is valid.
        $mode = strtolower($mode);
        if (!in_array($mode, $this->validModes, true)) {
            throw new \Exception('Invalid environment mode supplied or selected: ' . $mode);
        }

        $this->mode = $mode;
    }

    /**
     * Load and merge configuration files into one array.
     *
     * @return array $config array to be processed by setEnvironment
     * @throws \Exception
     */
    protected function getConfig()
    {
        $configMerged = [];
        foreach ($this->configDir as $configDir) {
            // Merge main config.
            $fileMainConfig = $configDir . 'main.php';
            if (!file_exists($fileMainConfig)) {
                throw new \Exception('Cannot find main config file "' . $fileMainConfig . '".');
            }
            $configMain = require($fileMainConfig);
            if (is_array($configMain)) {
                $configMerged = static::merge($configMerged, $configMain);
            }

            // Merge mode specific config.
            $fileSpecificConfig = $configDir . 'mode_' . $this->mode . '.php';
            if (!file_exists($fileSpecificConfig)) {
                throw new \Exception('Cannot find mode specific config file "' . $fileSpecificConfig . '".');
            }
            $configSpecific = require($fileSpecificConfig);
            if (is_array($configSpecific)) {
                $configMerged = static::merge($configMerged, $configSpecific);
            }

            // If one exists, merge local config.
            $fileLocalConfig = $configDir . 'local.php';
            if (file_exists($fileLocalConfig)) {
                $configLocal = require($fileLocalConfig);
                if (is_array($configLocal)) {
                    $configMerged = static::merge($configMerged, $configLocal);
                }
            }
        }

        return $configMerged;
    }

    /**
     * Sets the configuration for the selected mode.
     */
    protected function setEnvironment()
    {
        $this->config = $this->getConfig();
        $this->config['environment'] = $this->mode;
    }

    /**
     * Merges two or more arrays into one recursively.
     *
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     *
     * params: $a, $b [, array $... ]
     * $a array to be merged to
     * $b array to be merged from. You can specify additional arrays via third argument, fourth argument etc.
     *
     * @return array the merged array (the original arrays are not changed.)
     */
    protected static function merge()
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Show current Environment class values.
     */
    public function showDebug()
    {
        print '<div style="position: absolute; left: 0; width: 100%; height: 250px; overflow: auto;'
            . 'bottom: 0; z-index: 9999; color: #000; margin: 0; border-top: 1px solid #000;">'
            . '<pre style="margin: 0; background-color: #ddd; padding: 5px;">'
            . htmlspecialchars(print_r($this, true)) . '</pre></div>';
    }
}
