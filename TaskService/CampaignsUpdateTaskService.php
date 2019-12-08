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
use Mautic\CoreBundle\Helper\CoreParametersHelper;

class CampaignsUpdateTaskService extends AbstractTaskService
{
    public const COMMAND = 'mautic:campaigns:update';

    /**
     * @var TaskStatusProvider
     */
    private $taskStatusProvider;

    public function __construct(CoreParametersHelper $coreParametersHelper, TaskStatusProvider $taskStatusProvider)
    {
        parent::__construct($coreParametersHelper);
        $this->taskStatusProvider = $taskStatusProvider;
    }

    public function needsBackgroundJob(): bool
    {
        return $this->taskStatusProvider->campaignsAreActive();
    }
}
