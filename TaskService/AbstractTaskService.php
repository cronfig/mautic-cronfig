<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\TaskService;

use Symfony\Component\Routing\RouterInterface;
use MauticPlugin\CronfigBundle\Collection\TaskCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MauticPlugin\CronfigBundle\Api\DTO\Task;

abstract class AbstractTaskService implements TaskServiceInterface
{
    public const COMMAND = 'undefined';

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getCommand(): string
    {
        return static::COMMAND;
    }

    public function findActiveTasks(TaskCollection $allTasks): TaskCollection
    {
        $baseUrl = $this->router->generate('mautic_base_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $domain = trim(str_ireplace(['http://', 'https://'], '', $baseUrl), '/');

        return $allTasks->filter(function (Task $task) use ($domain) {
            return strpos($task->getUrl(), $domain.'/cronfig/'.urlencode($this->getCommand())) !== false;
        });
    }
}
