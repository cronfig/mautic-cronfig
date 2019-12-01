<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

use MauticPlugin\CronfigBundle\Collection\TaskServiceCollection;
use MauticPlugin\CronfigBundle\Provider\TaskServiceProvider;
use MauticPlugin\CronfigBundle\Api\Repository;
use MauticPlugin\CronfigBundle\TaskService\TaskServiceInterface;
use MauticPlugin\CronfigBundle\Api\DTO\Task;

class TaskManager
{
    /**
     * @var TaskServiceProvider
     */
    private $taskServiceProvider;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(TaskServiceProvider $taskServiceProvider, Repository $repository)
    {
        $this->taskServiceProvider = $taskServiceProvider;
        $this->repository          = $repository;
    }

    /**
     * Adds matching Tasks to the right TaskServices.
     *
     * @return TaskServiceCollection
     */
    public function setMatchingTasks(): TaskServiceCollection
    {
        $allTasks     = $this->repository->getTaskCollection();
        $taskServices = $this->taskServiceProvider->getTaskServiceCollection();

        return $taskServices->map(function (TaskServiceInterface $taskService) use ($allTasks) {
            return $taskService->setTasks($taskService->findMatchingTasks($allTasks));
        });
    }

    /**
     * Creates new tasks via API in the Cronfig.io service if there is a need for it.
     * Canceles active tasks if there is no need for them anymore.
     *
     * @return TaskServiceCollection
     */
    public function manageTasks(): TaskServiceCollection
    {
        $taskServices = $this->setMatchingTasks();

        return $taskServices->map(function (TaskServiceInterface $taskService) {
            $activeTasks = $taskService->getTasks()->filterByStatus(Task::STATUS_ACTIVE);

            if ($taskService->needsBackgroundJob() && $activeTasks->count() === 0) {
                $taskService->getTasks()->add(
                    $this->repository->activateTask($taskService->buildNewTask())
                );
            }

            if (!$taskService->needsBackgroundJob() && $activeTasks->count() > 0) {
                $this->repository->disableActiveTasks($activeTasks);
            }
        });
    }
}
