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

    /**
     * Tasks that exists in the Cronfig.io service.
     *
     * @var TaskCollection
     */
    protected $tasks;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->tasks = new TaskCollection([]);
    }

    public function getCommand(): string
    {
        return static::COMMAND;
    }

    public function findMatchingTasks(TaskCollection $allTasks): TaskCollection
    {
        $domain = str_ireplace(['http://', 'https://'], '', $this->getMauticUrl());

        return $allTasks->filter(function (Task $task) use ($domain) {
            return strpos($task->getUrl(), $domain.'/cronfig/'.urlencode($this->getCommand())) !== false;
        });
    }

    public function setTasks(TaskCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getTasks(): TaskCollection
    {
        return $this->tasks;
    }

    public function buildNewTask(): Task
    {
        $commandEncoded = urlencode($this->getCommand());
        $taskUrl = "{$this->getMauticUrl()}/cronfig/{$commandEncoded}?secret_key="; // @todo add the secret key.

        return new Task($taskUrl, Task::STATUS_ACTIVE, 'Mautic');
    }

    private function getMauticUrl(): string
    {
        return trim($this->router->generate('mautic_base_index', [], UrlGeneratorInterface::ABSOLUTE_URL), '/');
    }
}
