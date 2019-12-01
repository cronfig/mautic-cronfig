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
use MauticPlugin\CronfigBundle\TaskService\TaskServiceInterface;

/**
 * Holds TaskService objects that were added via DI tag.
 */
class TaskServiceCollection implements Iterator, Countable
{
    /**
     * @var TaskServiceInterface[]
     */
    private $records;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param TaskServiceInterface[] $recors
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function add(TaskServiceInterface $task): void
    {
        $this->records[] = $task;
    }

    public function map(callable $callback): TaskServiceCollection
    {
        array_map($callback, $this->records);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): TaskServiceInterface
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
