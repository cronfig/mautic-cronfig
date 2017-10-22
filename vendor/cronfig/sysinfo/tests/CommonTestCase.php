<?php

/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace CronfigTest\Sysinfo;

class CommonTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Custom method alias to swap the 2. and 7. argument
     */
    public function mockAbstractClass(
        $originalClassName,
        array $mockedMethods = [],
        $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = TRUE,
        $callOriginalClone = TRUE,
        $callAutoload = TRUE
    )
    {
        return parent::getMockForAbstractClass(
            $originalClassName,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $mockedMethods
        );
    }
}
