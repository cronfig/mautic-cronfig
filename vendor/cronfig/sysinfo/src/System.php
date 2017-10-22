<?php
/**
 * @package     Cronfig Sysinfo Library
 * @link        https://github.com/cronfig/sysinfo
 * @license     http://opensource.org/licenses/MIT
 */

namespace Cronfig\Sysinfo;

use Cronfig\Sysinfo\Linux;
use Cronfig\Sysinfo\Mac;
use Cronfig\Sysinfo\OsInterface;

/**
 * Class System
 */
class System
{
    /**
     * OS object representation matching current OS
     *
     * @var OsInterface
     */
    protected $os;

    /**
     * Constructor
     *
     * @param array $customs Array of custom OS class names can be added here
     */
    public function __construct(array $customs = [])
    {
        $defaults = [
            Linux::class,
            Mac::class,
        ];

        $this->registerOs(array_merge($customs, $defaults));
    }

    /**
     * Register array of OS class and pick the one in use
     *
     * @param  array  $classNames
     */
    public function registerOs(array $classNames)
    {
        foreach ($classNames as $className) {
            $os = new $className;

            if (!$os instanceof OsInterface) {
                throw new \UnexpectedValueException("Class {$className} must implement ".OsInterface::class);
            }

            if ($os->inUse()) {
                $this->os = $os;

                // The OS has been found, no need to iterate anymore
                break;
            }
        }
    }

    /**
     * Returns currently used OS object
     *
     * @return OsInterface
     */
    public function getOs()
    {
        return $this->os;
    }
}
