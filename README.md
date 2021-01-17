# Config service provider

[![Build Status](https://img.shields.io/github/workflow/status/misantron/silex-config-provider/build.svg?style=flat-square)](https://github.com/misantron/silex-config-provider/actions)
[![Code Coverage](https://img.shields.io/codacy/coverage/d0da1b65e553458ab7cea3758e9fd346.svg?style=flat-square)](https://app.codacy.com/gh/misantron/silex-config-provider/files)
[![Code Quality](https://img.shields.io/codacy/grade/d0da1b65e553458ab7cea3758e9fd346.svg?style=flat-square)](https://app.codacy.com/gh/misantron/silex-config-provider)
[![PHP Version](https://img.shields.io/packagist/php-v/misantron/silex-config-provider.svg?style=flat-square)](https://github.com/misantron/silex-config-provider)
[![Packagist](https://img.shields.io/packagist/v/misantron/silex-config-provider.svg?style=flat-square)](https://packagist.org/packages/misantron/silex-config-provider)

Config service provider for [Silex](http://silex.sensiolabs.org) PHP framework with multiple types support.  
Silex framework is DEPRECATED. Use [symfony/flex](https://github.com/symfony/flex) for future projects.

## Features

* Supported types of config files: php, json, ini, xml (require [libxml](https://www.php.net/manual/en/book.libxml.php) and [simplexml](https://www.php.net/manual/en/book.simplexml.php) extensions), toml (require [yosymfony/toml](https://github.com/yosymfony/toml)) and yaml (require [symfony/yaml](https://github.com/symfony/yaml))
* Using mixed types of configs inside one provider instance
* On load placeholder replacements
* Config key aliases
* Using environment variables for replacement

## Installation

The preferred way to install is through [Composer](https://getcomposer.org).
Run this command to install the latest stable version:

```bash
composer require misantron/silex-config-provider
```

## Documentation

Available [here](https://github.com/misantron/silex-config-provider/wiki)
