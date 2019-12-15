<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\Collection;

use MauticPlugin\CronfigBundle\Api\DTO\Task;
use MauticPlugin\CronfigBundle\Collection\TaskCollection;

class TaskCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testTaskCollectionFilterByStatus()
    {
        $taskColleciton = new TaskCollection([
            new Task('url1', Task::STATUS_ACTIVE, 'Mautic'),
            new Task('url2', Task::STATUS_STOPPED, 'Mautic'),
            new Task('url3', Task::STATUS_ACTIVE, 'Mautic'),
        ]);

        $this->assertCount(2, $taskColleciton->filterByStatus(Task::STATUS_ACTIVE));
        $this->assertCount(1, $taskColleciton->filterByStatus(Task::STATUS_STOPPED));
        $this->assertCount(0, $taskColleciton->filterByStatus(Task::STATUS_CANCELED));
        $this->assertSame('url2', $taskColleciton->filterByStatus(Task::STATUS_STOPPED)->current()->getUrl());
    }

    public function testTaskCollectionMap()
    {
        $originCollection = new TaskCollection([
            new Task('url1', Task::STATUS_STOPPED, 'Mautic'),
            new Task('url2', Task::STATUS_STOPPED, 'Mautic'),
        ]);

        $resultCollection = $originCollection->map(function (Task $task) {
            $task->setStatus(Task::STATUS_ACTIVE);

            return $task;
        });

        // It should be a copy of the collection. Immutable.
        $this->assertNotSame($originCollection, $resultCollection);

        // But the Tasks object should be the same reference.
        $this->assertSame($originCollection->current(), $resultCollection->current());
        $originCollection->next();
        $resultCollection->next();
        $this->assertSame($originCollection->current(), $resultCollection->current());
    }
}
