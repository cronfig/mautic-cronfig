<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Command;

use MauticPlugin\CronfigBundle\Api\DTO\Task;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use MauticPlugin\CronfigBundle\TaskService\TaskManager;
use Symfony\Component\Console\Helper\Table;

class TasksStatus extends ContainerAwareCommand
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    public function __construct(TaskManager $taskManager)
    {
        parent::__construct();
        $this->taskManager = $taskManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cronfig:tasks:status')
            ->setDescription('Finds tasks that need an active cron task to work and checks current status');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('command');

        $table = new Table($output);
        $table->setHeaders(['Command', 'Active', 'Active Tasks', 'Stopped Tasks', 'Canceled Tasks']);

        $taskServices = $this->taskManager->setMatchingTasks();

        foreach ($taskServices as $taskService) {
            $needsWorker = (int) $taskService->needsBackgroundJob();
            $tasks = $taskService->getTasks();
            $activeTasksCount = $tasks->filterByStatus(Task::STATUS_ACTIVE)->count();
            $stoppedTasksCount = $tasks->filterByStatus(Task::STATUS_STOPPED)->count();
            $canceledTasksCount = $tasks->filterByStatus(Task::STATUS_CANCELED)->count();
            $needsWorkerColor =  'white';
            $activeTasksColor = 'white';
            $stoppedTasksColor = 'white';
            if ($needsWorker) {
                $needsWorkerColor = 'green';
                if ($activeTasksCount) {
                    $activeTasksColor = 'green';
                } else {
                    $activeTasksColor = 'red';
                }
            }
            $canceledTasksColor = $canceledTasksCount ? 'red' : 'white';
            $table->addRow([
                $taskService->getCommand(),
                "<fg={$needsWorkerColor}>{$needsWorker}</>",
                "<fg={$activeTasksColor}>{$activeTasksCount}</>",
                "<fg={$stoppedTasksColor}>{$stoppedTasksCount}</>",
                "<fg={$canceledTasksColor}>{$canceledTasksCount}</>",
            ]);
        }

        $table->render();

        $event = $stopwatch->stop('command');

        $io->writeln("<fg=green>Execution time: {$event->getDuration()} ms</>");

        return 0;
    }
}
