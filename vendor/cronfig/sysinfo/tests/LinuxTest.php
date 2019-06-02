<?php

/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace CronfigTest\Sysinfo;

use Cronfig\Sysinfo\Linux;
use PHPUnit\Framework\Constraint\IsType;

class LinuxTest extends CommonTestCase
{
    public function testInUseTrue()
    {
        $os = $this->getMockBuilder(Linux::class)
            ->setMethods(['getCurrentOsName'])
            ->getMock();

        $os->expects($this->once())
            ->method('getCurrentOsName')
            ->will($this->returnValue('Linux'));

        $result = $os->inUse();

        $this->assertTrue($result);
    }

    public function testInUseFalse()
    {
        $os = $this->getMockBuilder(Linux::class)
            ->setMethods(['getCurrentOsName'])
            ->getMock();

        $os->expects($this->once())
            ->method('getCurrentOsName')
            ->will($this->returnValue('Windows'));

        $result = $os->inUse();

        $this->assertFalse($result);
    }

    public function testGetCoreCount()
    {
        $os = new Linux;

        // Sadly, we cannot test this on other OS than Linux
        if (!$os->inUse()) {
            $os = $this->getMockBuilder(Linux::class)
                ->setMethods(['getCoreCount'])
                ->getMock();

            $os->expects($this->once())
                ->method('getCoreCount')
                ->will($this->returnValue(2));
        }

        $result = $os->getCoreCount();
        $this->assertGreaterThanOrEqual(1, $result);
        $this->assertInternalType(IsType::TYPE_INT, $result);
    }
}
