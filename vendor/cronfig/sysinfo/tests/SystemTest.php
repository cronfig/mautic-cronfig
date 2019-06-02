<?php

/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace CronfigTest\Sysinfo;

use Cronfig\Sysinfo\Linux;
use Cronfig\Sysinfo\System;
use Cronfig\Sysinfo\OsInterface;

class SystemTest extends CommonTestCase
{
    public function testSystemFindsDefaultOs()
    {
        $this->assertTrue((new System)->getOs() instanceof OsInterface);
    }

    public function testSystemThrowsExceptionWhenRegisteringWrongOs()
    {
        $this->expectException(\UnexpectedValueException::class);
        new System([\StdClass::class]);
    }
}
