<?php

namespace Mnabialek\LaravelMultiConfig;

use Illuminate\Foundation\Bootstrap\DetectEnvironment as BaseDetectEnvironment;
use Dotenv\Dotenv;
use Dotenv\Loader;
use Illuminate\Contracts\Foundation\Application;

class DetectEnvironment extends BaseDetectEnvironment
{
    public function bootstrap(Application $app)
    {
        $env = null;

        $multiConfig = $app->make(MultiConfig::class);
        if (!env('APP_ENV')) {
            if ($multiConfig->get('env_mode') == 'env') {
                // we load default .env file and we assume current environment is
                // set in this file. After loading APP_ENV contains valid value
                (new Dotenv($app->environmentPath(),
                    $app->environmentFile()))->load();
                $env = env('APP_ENV');
            } elseif ($multiConfig->get('env_mode') == 'host') {
                // if host mode is used, we get environment name directly
                // from host name - if it's not set we use defaults
                $env = null;
                if (!$app->runningInConsole()) {
                    $env = $app->request->server('HTTP_HOST', null);
                }
                $env = str_replace(':', '', $env ?:
                    $multiConfig->get('no_host_default_environment'));
            }
        }

        // force Laravel to detect environment and include console arguments
        // (if any were used)
        $app->detectEnvironment(function () use ($env) {
            return $env;
        });

        if ($multiConfig->get('env_settings.load_env_file')) {
            // get valid env file name for current environment
            $envFileName = $this->getEnvFileName($app, $multiConfig);

            // here we finally set valid environment - based on env_mode and 
            // console arguments (if any were used) 
            (new Dotenv($multiConfig->get('paths.env_folder'),
                $envFileName))->overload();

            // we make sure that current environment is same as APP_ENV in
            // environment env file to prevent any issues
            if ($app->environment() != env('APP_ENV')) {
                if ($multiConfig->get('env_settings.verify_env_mismatch')) {
                    exit("ENV mismatch {$app->environment()} vs " .
                        env('APP_ENV') .
                        "\n");
                } else {
                    if ($multiConfig->get('env_settings.real_app_env')) {
                        $loader = new Loader(null, false);
                        $loader->setEnvironmentVariable(
                            $multiConfig->get('env_settings.real_app_env'),
                            $app->environment());
                    }
                }
            }
        } else {
            // if we don't want ENV file we should set APP_ENV to current
            // environment
            $loader = new Loader(null, false);
            $loader->setEnvironmentVariable('APP_ENV', $app->environment());
        }
    }

    /**
     * Get env file name for current environment
     *
     * @param Application $app
     * @param MultiConfig $multiConfig
     *
     * @return string
     */
    protected function getEnvFileName(
        Application $app,
        MultiConfig $multiConfig
    ) {
        $envFiles = $multiConfig->get('env_settings.environment_files');

        // set env file name - either in default format or user defined file
        $envFileName = isset($envFiles[$app->environment()]) ?
            $envFiles[$app->environment()] :
            '.' . $app->environment() . '.env';

        // if file not exists
        if (!file_exists($multiConfig->get('paths.env_folder') .
            DIRECTORY_SEPARATOR . $envFileName)
        ) {
            $showError = true;
            $fallBackEnv =
                $multiConfig->get('env_settings.fallback_environment');
            if ($fallBackEnv) {
                // if fallback env file is set and exits we will use this
                // instead of custom env file for environment
                if (file_exists($multiConfig->get('paths.env_folder') .
                    DIRECTORY_SEPARATOR . $fallBackEnv)) {
                    $envFileName = $fallBackEnv;
                    $showError = false;
                }
            }

            // if no custom env file or no fallback env file exists we will stop
            if ($showError) {
                exit("File {$envFileName} does not exist\n");
            }
        }

        return $envFileName;
    }
}
