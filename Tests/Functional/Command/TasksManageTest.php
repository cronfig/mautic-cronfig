<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use MauticPlugin\CronfigBundle\Command\TasksManage;
use MauticPlugin\CronfigBundle\Api\Config;
use MauticPlugin\CronfigBundle\Api\Connection;
use MauticPlugin\CronfigBundle\Provider\TaskStatusProvider;

class TasksManageTest extends KernelTestCase
{
    public function testNoTasksGetsCreatedIfMauticTasksAreOff()
    {
        $mePayload = [
            'data' => [
                'me' => [
                    'tasks' => [
                        'list' => [],
                    ],
                ],
            ],
        ];
        $apiConfig = $this->createMock(Config::class);
        $apiConnection = $this->createMock(Connection::class);
        $taskStatusProvider = $this->createMock(TaskStatusProvider::class);
        $apiConfig->method('getApiKey')->willReturn('test_api_key');
        $taskStatusProvider->method('segmentsAreActive')->willReturn(false);
        $taskStatusProvider->method('campaignsAreActive')->willReturn(false);
        $apiConnection->expects($this->exactly(1))
            ->method('query')
            ->with($this->getMeQuery())
            ->willReturn($mePayload);
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $container->set('cronfig.api.config', $apiConfig);
        $container->set('cronfig.api.connection', $apiConnection);
        $container->set('cronfig.provider.task_status', $taskStatusProvider);
        $application = new Application($kernel);
        $command = $application->find(TasksManage::COMMAND);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }

    public function testCronfigTasksGetCreatedIfNoneExist()
    {
        $mePayload = [
            'data' => [
                'me' => [
                    'tasks' => [
                        'list' => [],
                    ],
                ],
            ],
        ];
        $createSegmentTaskPayload = [
            'data' => [
                'createTask' => [
                    'id' => 'some_id',
                    'url' => 'http://mautic.test/cronfig/some-command',
                    'title' => 'Test task',
                    'platform' => 'Mautic',
                    'status' => 'active',
                    'period' => 30,
                    'timeout' => 5,
                    'createdAt' => '2019-12-08T20:24:00',
                    'updatedAt' => null,
                    'triggeredAt' => null,
                    'totalJobCount' => 1,
                    'totalErrorCount' => 0,
                    'errorCount' => 0,
                ],
            ],
        ];
        $apiConfig = $this->createMock(Config::class);
        $apiConnection = $this->createMock(Connection::class);
        $taskStatusProvider = $this->createMock(TaskStatusProvider::class);
        $apiConfig->method('getApiKey')->willReturn('test_api_key');
        $taskStatusProvider->method('segmentsAreActive')->willReturn(true);
        $taskStatusProvider->method('campaignsAreActive')->willReturn(true);
        $apiConnection->expects($this->exactly(4))
            ->method('query')
            ->withConsecutive(
                [$this->getMeQuery()],
                [$this->getCreateSegmentTaskQuery()],
                [$this->getCreateCampaignUpdateTaskQuery()],
                [$this->getCreateCampaignTriggerTaskQuery()]
            )
            ->willReturnOnConsecutiveCalls(
                $mePayload,
                $createSegmentTaskPayload,
                $createSegmentTaskPayload,
                $createSegmentTaskPayload
            );
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $container->set('cronfig.api.config', $apiConfig);
        $container->set('cronfig.api.connection', $apiConnection);
        $container->set('cronfig.provider.task_status', $taskStatusProvider);
        $application = new Application($kernel);
        $command = $application->find(TasksManage::COMMAND);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }

    // @todo create test to cover stopping the Cronfig task if the Mautic task is stopped.
    // @todo create test to cover activating the Cronfig task if the Mautic task is activated.

    private function getMeQuery(): string
    {
        return '{
    me {
        tasks(offset: 0 limit: 100) {
            pageInfo {
                total
            }
            list {
                id
                url
                status
                platform
                period
                timeout
                createdAt
                updatedAt
                triggeredAt
                totalJobCount
                totalErrorCount
                errorCount
            }
        }
    }
}';
    }

    private function getCreateSegmentTaskQuery(): string
    {
        return 'mutation {
    createTask(
        url: "http://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key="
        title: "Test task"
        platform: "Mautic"
    ) {
        id
        url
        status
        platform
        period
        timeout
        createdAt
        updatedAt
        triggeredAt
        totalJobCount
        totalErrorCount
        errorCount
    }
}';
    }

    private function getCreateCampaignUpdateTaskQuery(): string
    {
        return 'mutation {
    createTask(
        url: "http://mautic.test/cronfig/mautic%3Acampaigns%3Aupdate?secret_key="
        title: "Test task"
        platform: "Mautic"
    ) {
        id
        url
        status
        platform
        period
        timeout
        createdAt
        updatedAt
        triggeredAt
        totalJobCount
        totalErrorCount
        errorCount
    }
}';
    }

    private function getCreateCampaignTriggerTaskQuery(): string
    {
        return 'mutation {
    createTask(
        url: "http://mautic.test/cronfig/mautic%3Acampaigns%3Atrigger?secret_key="
        title: "Test task"
        platform: "Mautic"
    ) {
        id
        url
        status
        platform
        period
        timeout
        createdAt
        updatedAt
        triggeredAt
        totalJobCount
        totalErrorCount
        errorCount
    }
}';
    }
}
