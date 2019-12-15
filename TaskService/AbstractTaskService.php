<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Collection\TaskCollection;
use MauticPlugin\CronfigBundle\Api\DTO\Task;

abstract class AbstractTaskService implements TaskServiceInterface
{
    public const COMMAND = 'undefined';

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * Tasks that exists in the Cronfig.io service.
     *
     * @var TaskCollection
     */
    protected $tasks;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->tasks = new TaskCollection();
    }

    public function getCommand(): string
    {
        return static::COMMAND;
    }

    public function findMatchingTasks(TaskCollection $allTasks): TaskCollection
    {
        $domain = str_ireplace(['http://', 'https://'], '', $this->getMauticUrl());

        return $allTasks->filter(function (Task $task) use ($domain) {
            return false !== strpos($task->getUrl(), $domain.'/cronfig/'.urlencode($this->getCommand()));
        });
    }

    public function setTasks(TaskCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getTasks(): TaskCollection
    {
        return $this->tasks;
    }

    public function getTasksToCreate(): TaskCollection
    {
        $tasksToCreate = new TaskCollection();

        if (!$this->needsBackgroundJob()) {
            // This Mautic task does not need a Cronfig task to be created.
            return $tasksToCreate;
        }

        if ($this->getTasks()->filterByStatus(Task::STATUS_CANCELED)->count() > 0) {
            // If a task was canceled by the Cronfig service, only a human should
            // rewiew it and enable it again.
            return $tasksToCreate;
        }

        if ($this->getTasks()->filterByStatus(Task::STATUS_STOPPED)->count() > 0) {
            // There are some tasks that were previously stopped. Let the getTasksToUpdate()
            // method activate them rather than creating a new one.
            return $tasksToCreate;
        }

        if (0 === $this->getTasks()->filterByStatus(Task::STATUS_ACTIVE)->count()) {
            $commandEncoded = urlencode($this->getCommand());
            $taskUrl = "{$this->getMauticUrl()}/cronfig/{$commandEncoded}?secret_key="; // @todo add the secret key.

            $tasksToCreate->add(new Task($taskUrl, Task::STATUS_ACTIVE, 'Mautic'));
        }

        return $tasksToCreate;
    }

    public function getTasksToUpdate(): TaskCollection
    {
        $activeTasks = $this->getTasks()->filterByStatus(Task::STATUS_ACTIVE);
        $stoppedTasks = $this->getTasks()->filterByStatus(Task::STATUS_STOPPED);
        $needsTask = $this->needsBackgroundJob();

        if ($needsTask && 0 === $activeTasks->count() && $stoppedTasks->count() > 0) {
            $stoppedTasks->rewind();
            $taskToActivate = $stoppedTasks->current();
            $taskToActivate->setStatus(Task::STATUS_ACTIVE);

            return new TaskCollection([$taskToActivate]);
        }

        if (!$needsTask && $activeTasks->count() > 0) {
            return $activeTasks->map(function (Task $task) {
                $task->setStatus(Task::STATUS_STOPPED);

                return $task;
            });
        }

        return new TaskCollection();
    }

    private function getMauticUrl(): string
    {
        return trim($this->coreParametersHelper->getParameter('site_url'), '/');
    }
}
