<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

abstract class AbstractTaskService implements TaskServiceInterface
{
    public const COMMAND = 'undefined';

    public function getCommand(): string
    {
        return static::COMMAND;
    }
}
