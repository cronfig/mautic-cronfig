<?php
/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace Cronfig\Sysinfo;

/**
 * Class Mac
 */
class Mac extends AbstractOs implements OsInterface
{
    /**
     * Checks whether the current OS is equal to the current class
     *
     * @return bool
     */
    public function inUse()
    {
        return strtolower($this->getCurrentOsName()) === 'darwin';
    }

    /**
     * Counts CPU cores of the current system
     *
     * @return int
     */
    public function getCoreCount()
    {
        $this->requiredFunction('shell_exec');

        return (int) trim(shell_exec('sysctl -n hw.ncpu'));
    }
}
