<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Api;

use MauticPlugin\CronfigBundle\Api\DTO\Task;
use MauticPlugin\CronfigBundle\Collection\TaskCollection;
use MauticPlugin\CronfigBundle\Exception\ApiException;

final class Repository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(Connection $connection, QueryBuilder $queryBuilder)
    {
        $this->connection   = $connection;
        $this->queryBuilder = $queryBuilder;
    }

    public function getTaskCollection(): TaskCollection
    {
        return TaskCollection::makeFromApi(
            $this->connection->query(
                $this->queryBuilder->buildGetTasksQuery()
            )
        );
    }

    public function createTasks(TaskCollection $tasksToCreate): TaskCollection
    {
        $createdTasks = new TaskCollection();
        $tasksToCreate->map(function (Task $taskToCreate) use ($createdTasks) {
            try {
                $response = $this->connection->query(
                    $this->queryBuilder->buildCreateTasksQuery($taskToCreate)
                );

                $createdTasks->add(Task::makeFromArray($response['data']['createTask']));
            } catch (ApiException $e) {
                // @todo implement some logging. Throw it for now.
                throw $e;
            }
        });

        return $createdTasks;
    }

    public function updateTasks(TaskCollection $tasksToUpdate): TaskCollection
    {
        $updatedTasks = new TaskCollection();
        $tasksToUpdate->map(function (Task $taskToUpdate) use ($updatedTasks) {
            try {
                $response = $this->connection->query(
                    $this->queryBuilder->buildUpdateTasksQuery($taskToUpdate)
                );

                $updatedTasks->add(Task::makeFromArray($response['data']['updateTask']));
            } catch (ApiException $e) {
                // @todo implement some logging. Throw it for now.
                throw $e;
            }
        });

        return $updatedTasks;
    }
}
