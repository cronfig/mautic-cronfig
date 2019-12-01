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

class Repository
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
        $this->connection = $connection;
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

    public function activateTask(Task $task): Task
    {
        $response = $this->connection->query(
            $this->queryBuilder->buildCreateTasksQuery($task)
        );

        return Task::makeFromArray($response['data']['createTask']);
    }

    public function disableActiveTasks(TaskCollection $tasks): TaskCollection
    {
        // @todo
        return $tasks;
    }
}
