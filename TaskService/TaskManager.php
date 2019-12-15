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
use MauticPlugin\CronfigBundle\Collector\MauticTaskCollector;
use MauticPlugin\CronfigBundle\Api\Repository;

class TaskManager
{
    /**
     * @var MauticTaskCollector
     */
    private $mauticTaskCollector;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(MauticTaskCollector $mauticTaskCollector, Repository $repository)
    {
        $this->mauticTaskCollector = $mauticTaskCollector;
        $this->repository = $repository;
    }

    /**
     * Adds matching Tasks to the right TaskServices.
     */
    public function setMatchingTasks(): TaskServiceCollection
    {
        $allTasks = $this->repository->getTaskCollection();
        $taskServices = $this->mauticTaskCollector->getTaskServiceCollection();

        return $taskServices->map(function (TaskServiceInterface $taskService) use ($allTasks) {
            $taskService->setTasks($taskService->findMatchingTasks($allTasks));

            return $taskService;
        });
    }

    /**
     * Creates new tasks via API in the Cronfig.io service if there is a need for it.
     * Canceles active tasks if there is no need for them anymore.
     */
    public function manageTasks(): TaskServiceCollection
    {
        $taskServices = $this->setMatchingTasks();

        return $taskServices->map(function (TaskServiceInterface $taskService) {
            $this->repository->createTasks($taskService->getTasksToCreate());
            $this->repository->updateTasks($taskService->getTasksToUpdate());
        });
    }
}
