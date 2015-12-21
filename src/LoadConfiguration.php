<?php

namespace Mnabialek\LaravelMultiConfig;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Finder\Finder;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Foundation\Bootstrap\LoadConfiguration as BaseLoadConfiguration;

class LoadConfiguration extends BaseLoadConfiguration
{
    /**
     * We use code from parent function but we need to change detectEnvironment
     * to be used before loading any configuration files
     */
    public function bootstrap(Application $app)
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // here we set environment to APP_ENV before loading any configuration
        // files
        $app->detectEnvironment(function () {
            return env('APP_ENV', 'production');
        });

        $app->instance('config', $config = new Repository($items));

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        // we need to set this to make sure, we will have in config also 
        // valid name of environment (just in case anyone will use it)
        $config->set('app.env', $app->environment());

        date_default_timezone_set($config['app.timezone']);

        mb_internal_encoding('UTF-8');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfigurationFiles(Application $app)
    {
        /** @var MultiConfig $multiConfig */
        $multiConfig = $app->make(MultiConfig::class);

        // if config_mode is other than config we use default Laravel
        // method
        if ($multiConfig->get('config_mode') != 'config') {
            return parent::getConfigurationFiles($app);
        }

        $configPath = realpath($app->configPath());
        $files = [];

        // we merge default Laravel config path, extra config folders
        // and environment config directory
        $directories = array_merge(
            [$configPath],
            $multiConfig->get('config_extra_folders', []),
            [
                $multiConfig->get('paths.env_config_folder') .
                DIRECTORY_SEPARATOR . $app->environment(),
            ]
        );

        // if any of those directories does not exists we will ignore it
        foreach ($directories as $key => $val) {
            if (!file_exists($val)) {
                unset($directories[$key]);
            }
        }

        // we get files only in current directories (without subdirectories)
        foreach (Finder::create()->files()->depth('== 0')->name('*.php')
                     ->in($directories) as $file) {
            $files[basename($file->getRealPath(), '.php')][] =
                $file->getRealPath();
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadConfigurationFiles(
        Application $app,
        RepositoryContract $repository
    ) {
        /** @var MultiConfig $multiConfig */
        $multiConfig = $app->make(MultiConfig::class);

        // if config_mode is other than config we use default Laravel
        // method
        if ($multiConfig->get('config_mode') != 'config') {
            parent::loadConfigurationFiles($app, $repository);

            return;
        }

        // for each file we merge settings from all directories to get valid
        // configuration array
        foreach ($this->getConfigurationFiles($app) as $key => $paths) {
            $settings = [];

            foreach ($paths as $p) {
                $settings = array_replace_recursive($settings, require $p);
            }

            $repository->set($key, $settings);
        }
    }
}
