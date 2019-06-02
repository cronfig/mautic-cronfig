<?php
/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace Cronfig\Sysinfo;

/**
 * Abstract model of a general Operating System
 *
 * Class AbstractOs
 */
abstract class AbstractOs
{
    const TIMEFRAME_1_MIN = 0;
    const TIMEFRAME_5_MIN = 1;
    const TIMEFRAME_15_MIN = 2;

    /**
     * Checks whether the system has enough system load and memory for more work to do.
     * Less than 90% by default.
     *
     * @param  int $timeframe
     * @param  int $limit In percent
     *
     * @return bool
     */
    public function canHandleMore($timeframe, $limit = 90)
    {
        if ($this->getCurrentMemoryUsagePercentage() >= $limit) {
            return false;
        }

        if ($this->getLoadPercentage($timeframe) >= $limit) {
            return false;
        }

        return true;
    }

    /**
     * Returns the amount of memory allocated to PHP
     *
     * @return int in bytes
     */
    public function getCurrentMemoryUsage()
    {
        $this->requiredFunction('memory_get_usage');

        return memory_get_usage();
    }

    /**
     *  Returns the peak of memory allocated by PHP
     *
     * @return int in bytes
     */
    public function getPeakMemoryUsage()
    {
        $this->requiredFunction('memory_get_peak_usage');

        return memory_get_peak_usage();
    }

    /**
     * Finds out PHP memory limit from php.ini
     *
     * @return int in bytes
     */
    public function getMemoryLimit()
    {
        return $this->getBytesFromPhpIniValue(ini_get('memory_limit'));
    }

    /**
     * Finds out max PHP execution time limit from php.ini
     *
     * @return int in seconds. If set to zero, no time limit is imposed.
     */
    public function getExecutionTimeLimit()
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * Calculates current memory usage where 100% is the PHP memory limit
     *
     * @param int $round
     *
     * @return float
     */
    public function getCurrentMemoryUsagePercentage($round = 2)
    {
        return $this->getPercentage($this->getCurrentMemoryUsage(), $this->getMemoryLimit(), $round);
    }

    /**
     * Calculates peak memory usage where 100% is the PHP memory limit
     *
     * @param int $round
     *
     * @return float
     */
    public function getPeakMemoryUsagePercentage($round = 2)
    {
        return $this->getPercentage($this->getPeakMemoryUsage(), $this->getMemoryLimit(), $round);
    }

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
    public function getLoadPercentage($timeframe, $round = 2)
    {
        return $this->getPercentage($this->getLoad($timeframe), $this->getCoreCount(), $round);
    }

    /**
     * Gets system load. Implemented by PHP only on linux based systems.
     *
     * @param int $timeframe Use the constats of this class instead of integers
     *
     * @return int
     */
    public function getLoad($timeframe)
    {
        $this->requiredFunction('sys_getloadavg');

        $possibleArguments = [self::TIMEFRAME_1_MIN, self::TIMEFRAME_5_MIN, self::TIMEFRAME_15_MIN];

        if (!in_array($timeframe, $possibleArguments)) {
            throw new \UnexpectedValueException;
        }

        return sys_getloadavg()[$timeframe];
    }

    /**
     * Counts free disk percentage
     *
     * @param int $round
     *
     * @return float
     */
    public function getDiskUsagePercentage($round = 2)
    {
        $this->requiredFunction('disk_total_space')->requiredFunction('disk_free_space');

        $disktotal = disk_total_space('/');
        $diskfree  = disk_free_space('/');

        return 100 - $this->getPercentage($diskfree, $disktotal, $round);
    }

    /**
     * Counts CPU cores of the current system
     *
     * @return int
     */
    public function getCoreCount()
    {
        throw new \BadMethodCallException(__METHOD__.' must be implemented in a child class');
    }

    /**
     * Returns name of the current OS
     *
     * @return string
     */
    public function getCurrentOsName()
    {
        $this->requiredFunction('php_uname');
        return php_uname('s');
    }

    /**
     * Provides optimal timeframe for system load methods based on real execution time
     *
     * @param int $executionTime in seconds
     *
     * @return int
     */
    public function getTimeframeFromExecutionTime($executionTime)
    {
        $ETInMinutes = $executionTime / 60;

        if ($ETInMinutes > 10) {
            $timeframe = self::TIMEFRAME_15_MIN;
        } elseif ($ETInMinutes > 4) {
            $timeframe = self::TIMEFRAME_5_MIN;
        } else {
            $timeframe = self::TIMEFRAME_1_MIN;
        }

        return $timeframe;
    }

    /**
     * Helper method which counts percentage and ensures we don't devide by 0
     *
     * @param double $current
     * @param double $limit
     * @param int $round
     *
     * @return float
     */
    public function getPercentage($current, $limit, $round = 2)
    {
        if (!$limit) {
            return 0;
        }

        return round($current / $limit * 100, $round);
    }

    /**
     * Converts shorthand memory notation value to bytes
     *
     * @param string $val Memory size shorthand notation string
     *
     * @return int in bytes
     */
    public function getBytesFromPhpIniValue($val)
    {
        $val  = trim($val);
        $unit = strtolower($val[strlen($val) - 1]);
        $val  = (int) substr($val, 0, -1);

        switch ($unit) {
            case 'g':
                $val *= 1024;
                // no break;
            case 'm':
                $val *= 1024;
                // no break;
            case 'k':
                $val *= 1024;
                // no break;
        }

        return $val;
    }

    /**
     * Throws an exception if the function does not exist or is disabled
     *
     * @param string $name of the required function
     *
     * @return self
     */
    public function requiredFunction($name)
    {
        if (!function_exists($name)) {
            throw new \BadFunctionCallException('Function '.$name.' does not exist.');
        }

        if ($this->isFunctionDisabled($name)) {
            throw new \BadFunctionCallException('Function '.$name.' is disabled in php.ini.');
        }

        return $this;
    }

    /**
     * Check if the function is disabled in php.ini
     *
     * @param string $name
     *
     * @return bool
     */
    public function isFunctionDisabled($name)
    {
        $disabled = explode(',', ini_get('disable_functions'));
        return in_array($name, $disabled);
    }
}
