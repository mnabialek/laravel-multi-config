Laravel Multi Config
======
## Purpose

The purpose of this module is:

- allow to use multiple Configuration folders (as in Laravel 4)
- allow to use .env file specific for chosen environment
- allow to set current environment based on current HTTP_HOST

## Information

This module has not been tested yet. Basic testing was done for `Laravel 5.2` so if you want to use this module make sure you made backups of all your data and you really know what you are doing.

## Installation

1) Run:

`composer require `mnnabialek/laravel-multi-config` in your console

or add:

`"mnabialek/laravel-multi-config": "*"`

into `require` section of `composer.json`

2) Run `composer install` in console

3) Open `app/Http/Kernel.php` and:

* add `use Mnabialek\LaravelMultiConfig\BootstrappersChangerTrait;` before class definition
* add `use BootstrappersChangerTrait;` to use Module trait
* add:

```
    /**
     * {@inheritdoc}
     */
    protected function bootstrappers()
    {
        return $this->getCustomBootstrappers(parent::bootstrappers());
    }    
```
    
as new class method

4) Open `app/Console/Kernel.php` and to the same as in step 3

5) Open `config/app.php` and add add `Mnabialek\LaravelMultiConfig\ServiceProvider::class,`
 
 into `providers` section
 
6) Run

`php artisan vendor:publish --provider=Mnabialek\\LaravelMultiConfig\\ServiceProvider`

to publish module configuration file.


8) Open `config/multiconfig.php` and set it up according to your needs

## Configuration

Decide which `env_mode` you will use and which `config_mode` you will use (set it in `config/multiconfig.php`).
If you set `env_mode to `env` in `.env` file leave only `APP_ENV` (others you might want probably copy to env files specific to environments).

Create `.env` files specific to your environments. For example for local environment create `.local.env` file with `APP_ENV` set to `local` and any other values you want to use.

If you set `config_mode` to `config` in case you want to use custom environment configuration or extra configuration create proper directories.

## Issues

You cannot use this package to run Laravel for multiple domains at once. Extra changes need to be made to achieve that (details soon)

### Licence

This package is licenced under the [MIT license](http://opensource.org/licenses/MIT)
