<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\EventListener;

use Mautic\CampaignBundle\Event\CampaignEvent;
use MauticPlugin\CronfigBundle\EventListener\CampaignSubscriber;
use MauticPlugin\CronfigBundle\TaskService\TaskManager;
use PHPUnit\Framework\MockObject\MockObject;

class CampaignSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TaskManager|MockObject
     */
    private $taskManager;

    /**
     * @var CampaignSubscriber
     */
    private $campaignSubscriber;

    protected function setUp()
    {
        $this->taskManager        = $this->createMock(TaskManager::class);
        $this->campaignSubscriber = new CampaignSubscriber(
            $this->taskManager
        );
    }

    public function testOnChangeIfPublishedNotChanged()
    {
        /** @var CampaignEvent|MockObject $event */
        $event = $this->createMock(CampaignEvent::class);

        $this->taskManager->expects($this->never())
            ->method('manageTasks');

        $this->campaignSubscriber->onChange($event);
    }

    public function testOnChangeIfPublishedChangedToFalse()
    {
        /** @var CampaignEvent|MockObject $event */
        $event = $this->createMock(CampaignEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $event->expects($this->once())
            ->method('getChanges')
            ->willReturn(['isPublished' => false]);

        $this->campaignSubscriber->onChange($event);
    }

    public function testOnChangeIfPublishedChangedToTrue()
    {
        /** @var CampaignEvent|MockObject $event */
        $event = $this->createMock(CampaignEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $event->expects($this->once())
            ->method('getChanges')
            ->willReturn(['isPublished' => true]);

        $this->campaignSubscriber->onChange($event);
    }

    public function testOnDelete()
    {
        /** @var CampaignEvent|MockObject $event */
        $event = $this->createMock(CampaignEvent::class);

        $this->taskManager->expects($this->once())
            ->method('manageTasks');

        $this->campaignSubscriber->onDelete($event);
    }
}
