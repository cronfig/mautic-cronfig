<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignEvent;
use MauticPlugin\CronfigBundle\TaskService\TaskManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CampaignSubscriber implements EventSubscriberInterface
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    public function __construct(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_POST_SAVE   => 'onChange',
            CampaignEvents::CAMPAIGN_POST_DELETE => 'onDelete',
        ];
    }

    /**
     * @todo ensure it's being called on create (which is broken on M3 at the moment).
     * @todo managing all tasks is resource intensive. We could manage only to segment task.
     */
    public function onChange(CampaignEvent $campaignEvent): void
    {
        if (isset($campaignEvent->getChanges()['isPublished'])) {
            $this->taskManager->manageTasks();
        }
    }

    public function onDelete(CampaignEvent $campaignEvent): void
    {
        $this->taskManager->manageTasks();
    }
}
