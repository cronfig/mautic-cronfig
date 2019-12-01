<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

use MauticPlugin\CronfigBundle\Provider\TaskStatusProvider;

class SegmentsUpdateTaskService extends AbstractTaskService
{
    public const COMMAND = 'mautic:segments:update';

    /**
     * @var TaskStatusProvider
     */
    private $taskStatusProvider;

    public function __construct(TaskStatusProvider $taskStatusProvider)
    {
        $this->taskStatusProvider = $taskStatusProvider;
    }

    public function needsBackgroundJob(): bool
    {
        return $this->taskStatusProvider->segmentsAreActive();
    }
}
