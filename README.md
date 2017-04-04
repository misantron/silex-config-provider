# Config service provider
Config service provider for [Silex](http://silex.sensiolabs.org) framework with support for php, json and yaml

## Features

- Support different formats of config files: php, json and yaml (require symfony/yaml)
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