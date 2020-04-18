<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Collector;

use MauticPlugin\CronfigBundle\Collection\TaskServiceCollection;
use MauticPlugin\CronfigBundle\TaskService\TaskServiceInterface;

/**
 * Collects Mautic task services.
 */
final class MauticTaskCollector
{
    /**
     * @var TaskServiceCollection
     */
    private $taskServiceCollection;

    public function __construct()
    {
        $this->taskServiceCollection = new TaskServiceCollection();
    }

    public function addTask(TaskServiceInterface $taskService): void
    {
        $this->taskServiceCollection->add($taskService);
    }

    public function getTaskServiceCollection(): TaskServiceCollection
    {
        return $this->taskServiceCollection;
    }
}
