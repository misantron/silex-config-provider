# Config service provider

[![Build Status](https://img.shields.io/github/workflow/status/misantron/silex-config-provider/build.svg?style=flat-square)](https://github.com/misantron/silex-config-provider/actions)
[![Code Coverage](https://img.shields.io/codacy/coverage/d0da1b65e553458ab7cea3758e9fd346.svg?style=flat-square)](https://app.codacy.com/gh/misantron/silex-config-provider/files)
[![Code Quality](https://img.shields.io/codacy/grade/d0da1b65e553458ab7cea3758e9fd346.svg?style=flat-square)](https://app.codacy.com/gh/misantron/silex-config-provider)
[![PHP Version](https://img.shields.io/packagist/php-v/misantron/silex-config-provider.svg?style=flat-square)](https://github.com/misantron/silex-config-provider)
[![Packagist](https://img.shields.io/packagist/v/misantron/silex-config-provider.svg?style=flat-square)](https://packagist.org/packages/misantron/silex-config-provider)

Config service provider for [Silex](http://silex.sensiolabs.org) framework with different formats support.  
Silex framework is DEPRECATED. Use [symfony/flex](https://github.com/symfony/flex) for future projects.

## Features

* Support different formats of config files: php, json, ini, xml (require [libxml](https://www.php.net/manual/en/book.libxml.php) and [simplexml](https://www.php.net/manual/en/book.simplexml.php) extensions), toml (require [yosymfony/toml](https://github.com/yosymfony/toml)) and yaml (require [symfony/yaml](https://github.com/symfony/yaml))
* Multiple config file processing
* Ability to use mixed types of configs inside provider
* On load placeholder replacements
* Using environment variables for replacement

## Installation

The preferred way to install is through [Composer](https://getcomposer.org).
Run this command to install the latest stable version:

```shell
composer require misantron/silex-config-provider
```

## Usage

### Basic

```php
$provider = new \Misantron\Silex\Provider\ConfigServiceProvider([
    __DIR__ . '/../config/common.php',
]);

$app = new Application();
$app->register($provider);
```

### Merge configs with replacement

```php
$provider = new \Misantron\Silex\Provider\ConfigServiceProvider(
    [
        __DIR__ . '/../config/common.php',
        __DIR__ . '/../config/app.php',
    ],
    [
        'ROOT_PATH' => realpath(__DIR__ . '/..')
    ]
);

$app = new Application();
$app->register($provider);
```

### Replacement with env variables

```php
// config/app.php
return [
    'template.base' => '%env(TPL_ROOT)%',
];
```

```php
putenv('TPL_ROOT=/usr/path');

$root = '/app/path';
$provider = new \Misantron\Silex\Provider\ConfigServiceProvider(
    [
        __DIR__ . '/../config/app.php',
    ],
    [
        'ROOT_PATH' => $root,
    ]
);

$app = new Application();
$app->register($provider);

var_dump($app['template.base']); // /usr/path
```
