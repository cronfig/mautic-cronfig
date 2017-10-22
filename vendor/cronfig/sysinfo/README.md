System Info
=====

[![Latest Version](https://img.shields.io/github/release/cronfig/sysinfo.svg?style=flat-square)](https://github.com/cronfig/sysinfo/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/cronfig/sysinfo/master.svg?style=flat-square)](https://travis-ci.org/cronfig/sysinfo)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/cronfig/sysinfo.svg?style=flat-square)](https://scrutinizer-ci.com/g/cronfig/sysinfo/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/cronfig/sysinfo.svg?style=flat-square)](https://scrutinizer-ci.com/g/cronfig/sysinfo)
[![Total Downloads](https://img.shields.io/packagist/dt/cronfig/sysinfo.svg?style=flat-square)](https://packagist.org/packages/cronfig/sysinfo)

SysInfo is a simple library to get some info, metrics and available resources of the system the PHP code is running on.

Highlights
-------

* Simple API
* Framework-agnostic
* Composer ready, [PSR-2] and [PSR-4] compliant

System Requirements
-------

**PHP >= 7.0.10** is recommended. It's not required in composer.json so PHP projects stuck on older PHP versions could use it on their own consideration. The library does not work on PHP < 5.6.

This library use some native PHP functions like `shell_exec`, `php_uname`, `disk_total_space`, `disk_free_space`, `sys_getloadavg`, `memory_get_usage`, `memory_get_peak_usage` which may be disabled by some shared hostings.

`sys_getloadavg` is only available on linux based systems. Therefore **Windows is not supported**. The support can be added with new class.

Install
-------

Install `SysInfo` using Composer.

```
$ composer require cronfig/sysinfo
```

Usage
-------

```php
use Cronfig\Sysinfo\System;

// Instantiate the system
$system = new System;

// System can get you the OS you are currently running
$os = $system->getOs();

// Get some metrics like free disk space
$freeSpace = $os->getDiskUsagePercentage();
```

Testing
-------

`SysInfo` has a [PHPUnit](https://phpunit.de) test suite and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/). To run the tests, run the following command from the project folder.

```bash
$ composer test
```

#### Continuous integration

- [Travis CI](https://travis-ci.org/cronfig/sysinfo)
- [Scrutinizer CI](https://scrutinizer-ci.com/g/cronfig/sysinfo/)

License
-------

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[PSR-2]: http://www.php-fig.org/psr/psr-2/
[PSR-4]: http://www.php-fig.org/psr/psr-4/