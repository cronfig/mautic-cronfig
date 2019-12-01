<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use MauticPlugin\CronfigBundle\Provider\TaskServiceProvider;
use MauticPlugin\CronfigBundle\Api\Repository;
use Symfony\Component\Console\Helper\Table;

class TasksStatus extends ContainerAwareCommand
{
    /**
     * @var TaskServiceProvider
     */
    private $taskServiceProvider;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(TaskServiceProvider $taskServiceProvider, Repository $repository)
    {
        parent::__construct();
        $this->taskServiceProvider = $taskServiceProvider;
        $this->repository = $repository;
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
        
        $allTasks     = $this->repository->getTaskCollection();
        $taskServices = $this->taskServiceProvider->getTaskServiceCollection();
        $table        = new Table($output);
        $table->setHeaders(['Task', 'Needs worker', 'has worker']);

        foreach ($taskServices as $taskService) {
            $needsWorker      = $taskService->needsBackgroundJob();
            $activeTasks      = $taskService->findActiveTasks($allTasks);
            $activeTasksCount = $activeTasks->count();
            $needsWorkerColor = $needsWorker ? 'green' : 'yellow';
            $activeTasksColor = $activeTasksCount ? 'green' : 'yellow';
            $table->addRow([
                $taskService->getCommand(),
                "<fg={$needsWorkerColor}>{$needsWorker}</>",
                "<fg={$activeTasksColor}>{$activeTasksCount}</>",
            ]);
        }

        $table->render();

        $event = $stopwatch->stop('command');

        $io->writeln("<fg=green>Execution time: {$event->getDuration()} ms</>");

        return 0;
    }
}
