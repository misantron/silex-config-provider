# Config service provider

[![Build Status](http://img.shields.io/travis/misantron/silex-config-provider.svg?style=flat-square)](https://travis-ci.org/misantron/silex-config-provider)
[![Code Coverage](http://img.shields.io/coveralls/misantron/silex-config-provider.svg?style=flat-square)](https://coveralls.io/r/misantron/silex-config-provider)
[![Code Climate](http://img.shields.io/codeclimate/github/misantron/silex-config-provider.svg?style=flat-square)](https://codeclimate.com/github/misantron/silex-config-provider)
[![Packagist](https://img.shields.io/packagist/v/misantron/silex-config-provider.svg?style=flat-square)](https://packagist.org/packages/misantron/silex-config-provider)
[![PHP 7 Support](https://img.shields.io/badge/PHP%207-supported-blue.svg?style=flat-square)](https://travis-ci.org/misantron/silex-basic-app)

Config service provider for [Silex](http://silex.sensiolabs.org) framework with support for php, json, ini, xml and yaml

## Features

- Support different formats of config files: php, json, ini, xml and yaml (require symfony/yaml)
- Multiple config file processing
- On load placeholder replacements
- PHP7 support

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