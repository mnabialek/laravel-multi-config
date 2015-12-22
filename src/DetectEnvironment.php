<?php

namespace Mnabialek\LaravelMultiConfig;

use Illuminate\Foundation\Bootstrap\DetectEnvironment as BaseDetectEnvironment;
use Dotenv\Dotenv;
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

        // extra .env file for selected environment
        $envFileName = '.' . $app->environment() . '.env';

        if (!file_exists($multiConfig->get('paths.env_folder') .
            DIRECTORY_SEPARATOR .
            $envFileName)
        ) {
            exit("File {$envFileName} does not exist\n");
        }

        // here we finally set valid environment - based on env_mode and 
        // console arguments (if any were used) 
        (new Dotenv($app->environmentPath(), $envFileName))->overload();

        // we make sure that current environment is same as APP_ENV in
        // environment env file to prevent any issues
        if ($app->environment() != env('APP_ENV')) {
            exit("ENV mismatch {$env} vs " . env('APP_ENV') . "\n");
        }
    }
}
