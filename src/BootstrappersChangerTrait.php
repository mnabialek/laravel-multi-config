<?php

namespace Mnabialek\LaravelMultiConfig;

trait BootstrappersChangerTrait
{
    /**
     * Get modified bootstrappers to allow using MultiConfig module
     *
     * @param array $bootstrappers
     *
     * @return array
     */
    protected function getCustomBootstrappers(array $bootstrappers)
    {
        $multiConfig = $this->app->make(MultiConfig::Class);

        $changers = $multiConfig->get('bootstrappers');

        return array_map(function ($v) use ($changers) {
            return isset($changers[$v]) ? $changers[$v] : $v;
        }, $bootstrappers);
    }
}
