<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\EventListener;

use Mautic\LeadBundle\Event\LeadListEvent;
use MauticPlugin\CronfigBundle\EventListener\SegmentSubscriber;
use MauticPlugin\CronfigBundle\TaskService\TaskManager;
use PHPUnit\Framework\MockObject\MockObject;

class SegmentSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TaskManager|MockObject
     */
    private $taskManager;

    /**
     * @var SegmentSubscriber
     */
    private $segmentSubscriber;

    protected function setUp()
    {
        $this->taskManager       = $this->createMock(TaskManager::class);
        $this->segmentSubscriber = new SegmentSubscriber(
            $this->taskManager
        );
    }

    public function testOnChangeIfPublishedNotChanged()
    {
        /** @var LeadListEvent|MockObject $event */
        $event = $this->createMock(LeadListEvent::class);

        $this->taskManager->expects($this->never())
            ->method('manageTasks');

        $this->segmentSubscriber->onChange($event);
    }

    public function testOnChangeIfPublishedChangedToFalse()
    {
        /** @var LeadListEvent|MockObject $event */
        $event = $this->createMock(LeadListEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $event->expects($this->once())
            ->method('getChanges')
            ->willReturn(['isPublished' => false]);

        $this->segmentSubscriber->onChange($event);
    }

    public function testOnChangeIfPublishedChangedToTrue()
    {
        /** @var LeadListEvent|MockObject $event */
        $event = $this->createMock(LeadListEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $event->expects($this->once())
            ->method('getChanges')
            ->willReturn(['isPublished' => true]);

        $this->segmentSubscriber->onChange($event);
    }

    public function testOnDelete()
    {
        /** @var LeadListEvent|MockObject $event */
        $event = $this->createMock(LeadListEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $this->segmentSubscriber->onDelete($event);
    }
}
