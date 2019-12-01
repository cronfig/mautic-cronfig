<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

use MauticPlugin\CronfigBundle\Collection\TaskCollection;

interface TaskServiceInterface
{
    public function getCommand(): string;

    public function needsBackgroundJob(): bool;

    /**
     * Finds all active Cronfig tasks that are triggering this particular Mautic task.
     *
     * @param TaskCollection $allTasks
     *
     * @return TaskCollection
     */
    public function findActiveTasks(TaskCollection $allTasks): TaskCollection;
}
