# env

Environment class, used to set configuration depending on the server environment.

[![Software License](https://img.shields.io/badge/license-Unlicense-blue.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/janisto/env/master.svg?style=flat-square)](https://travis-ci.org/janisto/env)
[![Code Quality](https://img.shields.io/scrutinizer/g/janisto/env.svg?style=flat-square)](https://scrutinizer-ci.com/g/janisto/env)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/janisto/env.svg?style=flat-square)](https://scrutinizer-ci.com/g/janisto/env)
[![Packagist Version](https://img.shields.io/packagist/v/janisto/env.svg?style=flat-square)](https://packagist.org/packages/janisto/env)
[![Total Downloads](https://img.shields.io/packagist/dt/janisto/env.svg?style=flat-square)](https://packagist.org/packages/janisto/env)

## Installation

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this package using the following command:

```php
php composer.phar require "janisto/env" "*"
```
or add

```json
"janisto/env": "*"
```

to the require section of your application's `composer.json` file.

## Usage

### web index.php

```php
<?php
require(dirname(__DIR__) . '/vendor/autoload.php');

$env = new \janisto\env\Environment(dirname(__DIR__) . '/config');
// $env->config // environment configuration array
```

or if you have multiple configuration locations

```php
<?php
require(dirname(__DIR__) . '/vendor/autoload.php');

$env = new \janisto\env\Environment([
    dirname(__DIR__) . '/common/config',
    dirname(__DIR__) . '/backend/config'
]);
// $env->config // environment configuration array
```

### console cli.php

```php
#!/usr/bin/env php
<?php

require(__DIR__ . '/vendor/autoload.php');

$env = new \janisto\env\Environment(__DIR__ . '/config');
// $env->config // environment configuration array
```

Use cli.php

```
export APP_ENV='dev' && ./cli.php
```

## Documentation

See `examples/`.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Jani Mikkonen](https://github.com/janisto)
- [All Contributors](../../contributors)

## License

Public domain. Please see [License File](LICENSE.md) for more information.
