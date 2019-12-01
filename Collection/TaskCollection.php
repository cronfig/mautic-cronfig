<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Collection;

use Iterator;
use Countable;
use MauticPlugin\CronfigBundle\Api\DTO\Task;

/**
 * Holds Task objects that represent tasks sent from or to the Cronfig API.
 */
class TaskCollection implements Iterator, Countable
{
    /**
     * @var Task[]
     */
    private $records;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param Task[] $recors
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public static function makeFromApi(array $payload): TaskCollection
    {
        $taskCollection = new self([]);

        foreach ($payload['data']['me']['tasks']['list'] as $taskArray) {
            $taskCollection->add(Task::makeFromArray($taskArray));
        }

        return $taskCollection;
    }

    public function add(Task $task): void
    {
        $this->records[] = $task;
    }

    public function filter(callable $callback): TaskCollection
    {
        return new self(array_filter($this->records, $callback));
    }

    /**
     * {@inheritdoc}
     */
    public function current(): Task
    {
        return $this->records[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->records[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->records);
    }
}
