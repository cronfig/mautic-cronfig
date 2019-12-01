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
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MauticPlugin\CronfigBundle\Api\DTO\Task;

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

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(TaskServiceProvider $taskServiceProvider, Repository $repository, RouterInterface $router)
    {
        parent::__construct();
        $this->taskServiceProvider = $taskServiceProvider;
        $this->repository = $repository;
        $this->router = $router;
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

        $baseUrl = $this->router->generate('mautic_base_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $domain = trim(str_ireplace(['http://', 'https://'], '', $baseUrl), '/');
        $allTasks = $this->repository->getTaskCollection();
        $taskServices = $this->taskServiceProvider->getTaskServiceCollection();
        $table        = new Table($output);
        $table->setHeaders(['Task', 'Needs worker', 'has worker']);

        foreach ($taskServices as $taskService) {
            $needsWorker = $taskService->needsBackgroundJob();
            $needsWorkerColor = $needsWorker ? 'green' : 'yellow';
            $command = $taskService->getCommand();
            $activeTasks = $allTasks->filter(function(Task $task) use ($domain, $command) {
                return strpos($task->getUrl(), $domain.'/cronfig/'.urlencode($command)) !== false;
            });
            $activeTasksCount = $activeTasks->count();
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
