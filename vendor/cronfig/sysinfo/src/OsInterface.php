<?php
/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace Cronfig\Sysinfo;

/**
 * Interface OsInterface
 */
interface OsInterface
{
    /**
     * Checks whether the current OS is equal to the current class
     *
     * @return bool
     */
    public function inUse();

    /**
     * Returns the amount of memory allocated to PHP
     *
     * @return int in bytes
     */
    public function getCurrentMemoryUsage();

    /**
     *  Returns the peak of memory allocated by PHP
     *
     * @return int in bytes
     */
    public function getPeakMemoryUsage();

    /**
     * Finds out PHP memory limit from php.ini
     *
     * @return int in bytes
     */
    public function getMemoryLimit();

    /**
     * Calculates current memory usage where 100% is the PHP memory limit
     *
     * @param int $round
     *
     * @return float
     */
    public function getCurrentMemoryUsagePercentage();

    /**
     * Calculates peak memory usage where 100% is the PHP memory limit
     *
     * @param int $round
     *
     * @return float
     */
    public function getPeakMemoryUsagePercentage();

    /**
     * Returns system load divided by CPU cores to get percentage per core.
     * value < 100% means system handles the processes fine
     * value > 100% means system is overloaded and some processes are waiting for processing time
     *
     * @param int $timeframe Use the constats of this class instead of integers
     * @param int $round
     *
     * @return float
     */
    public function getLoadPercentage($timeframe, $round = 2);

    /**
     * Gets system load. Implemented by PHP only on linux based systems.
     *
     * @param int $timeframe Use the constats of this class instead of integers
     *
     * @return int
     */
    public function getLoad($timeframe);

    /**
     * Counts CPU cores of the current system
     *
     * @return int
     */
    public function getCoreCount();

    /**
     * Returns name of the current OS
     *
     * @return string
     */
    public function getCurrentOsName();

    /**
     * Provides optimal timeframe for system load methods based on real execution time
     *
     * @param int $executionTime in seconds
     *
     * @return int
     */
    public function getTimeframeFromExecutionTime($executionTime);

    /**
     * Helper method which counts percentage and ensures we don't devide by 0
     *
     * @param double $current
     * @param double $limit
     * @param int $round
     *
     * @return float
     */
    public function getPercentage($current, $limit, $round = 2);

    /**
     * Converts shorthand memory notation value to bytes
     *
     * @param string $val Memory size shorthand notation string
     *
     * @return int in bytes
     */
    public function getBytesFromPhpIniValue($val);

    /**
     * Throws an exception if the function does not exist or is disabled
     *
     * @param string $name of the required function
     *
     * @return self
     */
    public function requiredFunction($name);

    /**
     * Check if the function is disabled in php.ini
     *
     * @param string $name
     *
     * @return bool
     */
    public function isFunctionDisabled($name);
}
