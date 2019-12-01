# Config service provider

[![Build Status](http://img.shields.io/travis/com/misantron/silex-config-provider.svg?style=flat-square)](https://travis-ci.com/misantron/silex-config-provider)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/misantron/silex-config-provider.svg?style=flat-square)](https://scrutinizer-ci.com/g/misantron/silex-config-provider)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/misantron/silex-config-provider.svg?style=flat-square)](https://scrutinizer-ci.com/g/misantron/silex-config-provider)
[![PHP Version](https://img.shields.io/travis/php-v/misantron/silex-config-provider.svg?style=flat-square)](https://github.com/misantron/silex-config-provider)
[![Packagist](https://img.shields.io/packagist/v/misantron/silex-config-provider.svg?style=flat-square)](https://packagist.org/packages/misantron/silex-config-provider)

Config service provider for [Silex](http://silex.sensiolabs.org) framework with support for php, json, ini, xml, toml and yaml.  
Silex framework is DEPRECATED. Use [symfony/flex](https://github.com/symfony/flex) for future projects.

## Features

- Support different formats of config files: php, json, ini, xml, toml (require [leonelquinteros/php-toml](https://github.com/leonelquinteros/php-toml)) and yaml (require [symfony/yaml](https://github.com/symfony/yaml))
- Multiple config file processing
- On load placeholder replacements

## Installation

The preferred way to install is through [Composer](https://getcomposer.org).
Run this command to install the latest stable version:

```shell
$ composer require misantron/silex-config-provider
```

## Usage

```php
$provider = new \Misantron\Silex\Provider\ConfigServiceProvider(
    new \Misantron\Silex\Provider\Adapter\PhpConfigAdapter(),
    [
        __DIR__ . '/../config/common.php',
        __DIR__ . '/../config/app.php',
    ],
    [
        'root_path' => realpath(__DIR__ . '/..')
    ]
);

$app = new Application();
$app->register($provider);
```