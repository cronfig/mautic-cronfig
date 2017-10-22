<?php
/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace Cronfig\Sysinfo;

/**
 * Class Linux
 */
class Linux extends AbstractOs implements OsInterface
{
    /**
     * Checks whether the current OS is equal to the current class
     *
     * @return bool
     */
    public function inUse()
    {
        return strtolower($this->getCurrentOsName()) === 'linux';
    }

    /**
     * Counts CPU cores of the current system
     *
     * @return int
     */
    public function getCoreCount()
    {
        return (int) trim(shell_exec('nproc --all'));
    }
}
