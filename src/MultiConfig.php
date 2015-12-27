<?php

namespace Mnabialek\LaravelMultiConfig;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class MultiConfig
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * Module configuration settings
     *
     * @var array
     */
    protected $config = [];

    /**
     * Initialize class
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->loadConfiguration();
    }

    /**
     * Get module configuration value
     *
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get default configuration file path
     *
     * @return string
     */
    public function getDefaultConfigFilePath()
    {
        return __DIR__ . '../../config/multiconfig.php';
    }

    /**
     * Get user configuration file path
     *
     * @return string
     */
    public function getConfigFilePath()
    {
        return config_path('multiconfig.php');
    }

    /**
     * Load module configuration - we merge here any published configuration
     * with module default configuration file
     */
    protected function loadConfiguration()
    {
        // get user configuration file
        $userConfig = [];
        $path = $this->getConfigFilePath();
        if (file_exists($path)) {
            $userConfig = require $path;
        }

        // module default configuration
        $config = require $this->getDefaultConfigFilePath();

        // merge user and default configuration
        $this->config = array_replace_recursive($config, $userConfig);
    }
}
