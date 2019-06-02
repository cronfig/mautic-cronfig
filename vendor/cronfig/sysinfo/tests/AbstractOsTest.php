<?php

/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace CronfigTest\Sysinfo;

use Cronfig\Sysinfo\AbstractOs;
use PHPUnit\Framework\Constraint\IsType;

class AbstractOsTest extends CommonTestCase
{
    public function testCanHandleMoreWithFreeResources()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getCurrentMemoryUsagePercentage', 'getLoadPercentage']);

        $os->expects($this->once())
            ->method('getCurrentMemoryUsagePercentage')
            ->willReturn(50);

        $os->expects($this->once())
            ->method('getLoadPercentage')
            ->will($this->returnValue(80));

        $this->assertTrue($os->canHandleMore(AbstractOs::TIMEFRAME_1_MIN));
    }

    public function testCanHandleMoreWithOverloadedMemory()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getCurrentMemoryUsagePercentage', 'getLoadPercentage']);

        $os->expects($this->once())
            ->method('getCurrentMemoryUsagePercentage')
            ->willReturn(94);

        $os->expects($this->never())
            ->method('getLoadPercentage');

        $this->assertFalse($os->canHandleMore(AbstractOs::TIMEFRAME_5_MIN));
    }

    public function testCanHandleMoreWithOverloadedSystemLoad()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getCurrentMemoryUsagePercentage', 'getLoadPercentage']);

        $os->expects($this->once())
            ->method('getCurrentMemoryUsagePercentage')
            ->willReturn(91);

        $os->expects($this->once())
            ->method('getLoadPercentage')
            ->willReturn(95);

        $this->assertFalse($os->canHandleMore(AbstractOs::TIMEFRAME_15_MIN, 94));
    }

    public function testGetCurrentMemoryUsage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getPeakMemoryUsage();

        $this->assertInternalType(IsType::TYPE_INT, $result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetPeakMemoryUsage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getCurrentMemoryUsage();

        $this->assertInternalType(IsType::TYPE_INT, $result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetMemoryLimit()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getMemoryLimit();

        $this->assertInternalType(IsType::TYPE_INT, $result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetExecutionTimeLimit()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getExecutionTimeLimit();

        $this->assertInternalType(IsType::TYPE_INT, $result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testGetCurrentMemoryUsagePercentage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getCurrentMemoryUsagePercentage();

        $this->assertInternalType(IsType::TYPE_FLOAT, $result);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetCurrentMemoryUsagePercentageWithMockData()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getCurrentMemoryUsage', 'getMemoryLimit']);

        $os->expects($this->once())
            ->method('getCurrentMemoryUsage')
            ->willReturn(50);

        $os->expects($this->once())
            ->method('getMemoryLimit')
            ->will($this->returnValue(100));

        $result = $os->getCurrentMemoryUsagePercentage();

        $this->assertEquals(50, $result);
    }

    public function testGetPeakMemoryUsagePercentage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getPeakMemoryUsagePercentage();

        $this->assertInternalType(IsType::TYPE_FLOAT, $result);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetPeakMemoryUsagePercentageWithMockData()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getPeakMemoryUsage', 'getMemoryLimit']);

        $os->expects($this->once())
            ->method('getPeakMemoryUsage')
            ->willReturn(50);

        $os->expects($this->once())
            ->method('getMemoryLimit')
            ->will($this->returnValue(100));

        $result = $os->getPeakMemoryUsagePercentage();

        $this->assertEquals(50, $result);
    }

    public function testGetLoadPercentageWithoutCoreCountMethodImplemented()
    {
        $this->expectException(\BadMethodCallException::class);
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getLoadPercentage(AbstractOs::TIMEFRAME_5_MIN);
    }

    public function testGetLoadPercentage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class, ['getCoreCount']);

        $os->expects($this->once())
            ->method('getCoreCount')
            ->will($this->returnValue(2));

        $result = $os->getLoadPercentage(AbstractOs::TIMEFRAME_5_MIN);

        $this->assertInternalType(IsType::TYPE_FLOAT, $result);
        $this->assertGreaterThan(0, $result);
        // System load percentage can go over 100%
    }

    public function testGetLoadWithWrongValue()
    {
        $this->expectException(\UnexpectedValueException::class);
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getLoad(3);
    }

    public function testGetLoad()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getLoad(AbstractOs::TIMEFRAME_1_MIN);

        $this->assertInternalType(IsType::TYPE_FLOAT, $result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetBytesFromPhpIniValue()
    {
        $values = [
            '1024K' => 1048576,
            '512M' => 536870912,
            '1G' => 1073741824,
        ];

        $os = $this->mockAbstractClass(AbstractOs::class);

        foreach ($values as $value => $expected) {
            $result = $os->getBytesFromPhpIniValue($value);
            $this->assertEquals($expected, $result);
        }
    }

    public function testGetDiskUsagePercentage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getDiskUsagePercentage();

        $this->assertInternalType(IsType::TYPE_FLOAT, $result);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThanOrEqual(100, $result);
    }

    public function testGetCurrentOsName()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);
        $result = $os->getCurrentOsName();

        $this->assertInternalType(IsType::TYPE_STRING, $result);
        $this->assertGreaterThan(0, strlen($result));
    }

    public function testGetTimeframeFromExecutionTime()
    {
        $values = [
            1 => 0,
            30 => 0,
            60 => 0,
            90 => 0,
            240 => 0,
            250 => 1,
            600 => 1,
            700 => 2,
        ];

        $os = $this->mockAbstractClass(AbstractOs::class);

        foreach ($values as $value => $expected) {
            $result = $os->getTimeframeFromExecutionTime($value);
            $this->assertEquals($expected, $result, "For value $value we expect $expected, but got $result");
        }
    }

    public function testGetPercentage()
    {
        $os = $this->mockAbstractClass(AbstractOs::class);

        $result = $os->getPercentage(0, 0, 0);
        $this->assertEquals(0, $result);

        $result = $os->getPercentage(20, 60, 3);
        $this->assertEquals(33.333, $result);
    }
}
