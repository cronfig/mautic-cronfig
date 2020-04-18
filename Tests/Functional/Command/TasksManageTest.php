<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Functional\Command;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Api\Config;
use MauticPlugin\CronfigBundle\Api\Connection;
use MauticPlugin\CronfigBundle\Api\DTO\Task;
use MauticPlugin\CronfigBundle\Command\TasksManage;
use MauticPlugin\CronfigBundle\Provider\TaskStatusProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TasksManageTest extends KernelTestCase
{
    /**
     * @var Config|MockObject
     */
    private $apiConfig;

    /**
     * @var Connection|MockObject
     */
    private $apiConnection;

    /**
     * @var TaskStatusProvider|MockObject
     */
    private $taskStatusProvider;

    /**
     * @var CoreParametersHelper|MockObject
     */
    private $coreParametersHelper;

    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiConfig            = $this->createMock(Config::class);
        $this->apiConnection        = $this->createMock(Connection::class);
        $this->taskStatusProvider   = $this->createMock(TaskStatusProvider::class);
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);

        $kernel    = static::bootKernel();
        $container = $kernel->getContainer();

        $container->set('cronfig.api.config', $this->apiConfig);
        $container->set('cronfig.api.connection', $this->apiConnection);
        $container->set('cronfig.provider.task_status', $this->taskStatusProvider);
        $container->set('mautic.helper.core_parameters', $this->coreParametersHelper);

        $application         = new Application($kernel);
        $this->commandTester = new CommandTester($application->find(TasksManage::COMMAND));
    }

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

        $this->apiConfig->method('getApiKey')->willReturn('test_api_key');
        $this->taskStatusProvider->method('segmentsAreActive')->willReturn(false);
        $this->taskStatusProvider->method('campaignsAreActive')->willReturn(false);
        $this->taskStatusProvider->method('ipLookupDownloadShouldBeActive')->willReturn(false);
        $this->coreParametersHelper->method('getParameter')->with('site_url')->willReturn('https://mautic.test');
        $this->apiConnection->expects($this->exactly(1))
            ->method('query')
            ->with($this->getMeQuery())
            ->willReturn($mePayload);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCronfigTasksGetCreatedIfNoneCronfigTasksExist()
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
                    'id'              => 'some_id',
                    'url'             => 'https://mautic.test/cronfig/some-command',
                    'title'           => 'Test task',
                    'platform'        => 'Mautic',
                    'status'          => Task::STATUS_ACTIVE,
                    'period'          => 30,
                    'timeout'         => 5,
                    'createdAt'       => '2019-12-08T20:24:00',
                    'updatedAt'       => null,
                    'triggeredAt'     => null,
                    'totalJobCount'   => 1,
                    'totalErrorCount' => 0,
                    'errorCount'      => 0,
                ],
            ],
        ];
        $this->apiConfig->method('getApiKey')->willReturn('test_api_key');
        $this->taskStatusProvider->method('segmentsAreActive')->willReturn(true);
        $this->taskStatusProvider->method('campaignsAreActive')->willReturn(true);
        $this->taskStatusProvider->method('ipLookupDownloadShouldBeActive')->willReturn(false);
        $this->coreParametersHelper->method('getParameter')->with('site_url')->willReturn('https://mautic.test');
        $this->apiConnection->expects($this->exactly(4))
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
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    /**
     * Test create test to cover stopping the Cronfig task if the Mautic task is stopped.
     */
    public function testExistingCronfigTasksGetStoppedIfMauticTasksAreOff()
    {
        $mePayload = [
            'data' => [
                'me' => [
                    'tasks' => [
                        'list' => [
                            [
                                'id'              => 'some_id',
                                'url'             => 'https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key=',
                                'title'           => 'Test task',
                                'platform'        => 'Mautic',
                                'status'          => Task::STATUS_ACTIVE,
                                'period'          => 30,
                                'timeout'         => 5,
                                'createdAt'       => '2019-12-08T20:24:00',
                                'updatedAt'       => null,
                                'triggeredAt'     => null,
                                'totalJobCount'   => 1,
                                'totalErrorCount' => 0,
                                'errorCount'      => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $stopSegmentTaskPayload = [
            'data' => [
                'updateTask' => [
                    'id'              => 'some_id',
                    'url'             => 'https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key=',
                    'title'           => 'Test task',
                    'platform'        => 'Mautic',
                    'status'          => Task::STATUS_STOPPED,
                    'period'          => 30,
                    'timeout'         => 5,
                    'createdAt'       => '2019-12-08T20:24:00',
                    'updatedAt'       => null,
                    'triggeredAt'     => null,
                    'totalJobCount'   => 1,
                    'totalErrorCount' => 0,
                    'errorCount'      => 0,
                ],
            ],
        ];
        $this->apiConfig->method('getApiKey')->willReturn('test_api_key');
        $this->taskStatusProvider->method('segmentsAreActive')->willReturn(false);
        $this->taskStatusProvider->method('campaignsAreActive')->willReturn(false);
        $this->taskStatusProvider->method('ipLookupDownloadShouldBeActive')->willReturn(false);
        $this->coreParametersHelper->method('getParameter')->with('site_url')->willReturn('https://mautic.test');
        $this->apiConnection->expects($this->exactly(2))
            ->method('query')
            ->withConsecutive(
                [$this->getMeQuery()],
                [$this->getStopSegmentTaskQuery()]
            )
            ->willReturnOnConsecutiveCalls(
                $mePayload,
                $stopSegmentTaskPayload
            );
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    /**
     * Test create test to cover activating the Cronfig task if the Mautic task is activated.
     */
    public function testExistingCronfigTasksGetStartedIfMauticTasksAreOn()
    {
        $mePayload = [
            'data' => [
                'me' => [
                    'tasks' => [
                        'list' => [
                            [
                                'id'              => 'some_id',
                                'url'             => 'https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key=',
                                'title'           => 'Test task',
                                'platform'        => 'Mautic',
                                'status'          => Task::STATUS_STOPPED,
                                'period'          => 30,
                                'timeout'         => 5,
                                'createdAt'       => '2019-12-08T20:24:00',
                                'updatedAt'       => null,
                                'triggeredAt'     => null,
                                'totalJobCount'   => 1,
                                'totalErrorCount' => 0,
                                'errorCount'      => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $stopSegmentTaskPayload = [
            'data' => [
                'updateTask' => [
                    'id'              => 'some_id',
                    'url'             => 'https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key=',
                    'title'           => 'Test task',
                    'platform'        => 'Mautic',
                    'status'          => Task::STATUS_ACTIVE,
                    'period'          => 30,
                    'timeout'         => 5,
                    'createdAt'       => '2019-12-08T20:24:00',
                    'updatedAt'       => null,
                    'triggeredAt'     => null,
                    'totalJobCount'   => 1,
                    'totalErrorCount' => 0,
                    'errorCount'      => 0,
                ],
            ],
        ];
        $this->apiConfig->method('getApiKey')->willReturn('test_api_key');
        $this->taskStatusProvider->method('segmentsAreActive')->willReturn(true);
        $this->taskStatusProvider->method('campaignsAreActive')->willReturn(false);
        $this->taskStatusProvider->method('ipLookupDownloadShouldBeActive')->willReturn(false);
        $this->coreParametersHelper->method('getParameter')->with('site_url')->willReturn('https://mautic.test');
        $this->apiConnection->expects($this->exactly(2))
            ->method('query')
            ->withConsecutive(
                [$this->getMeQuery()],
                [$this->getActivateSegmentTaskQuery()]
            )
            ->willReturnOnConsecutiveCalls(
                $mePayload,
                $stopSegmentTaskPayload
            );
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

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
        url: "https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key="
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
        url: "https://mautic.test/cronfig/mautic%3Acampaigns%3Aupdate?secret_key="
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
        url: "https://mautic.test/cronfig/mautic%3Acampaigns%3Atrigger?secret_key="
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

    private function getStopSegmentTaskQuery(): string
    {
        return 'mutation {
    updateTask(
        id: "some_id"
        url: "https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key="
        platform: "Mautic"
        status: "stopped"
        period: 30
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

    private function getActivateSegmentTaskQuery(): string
    {
        return 'mutation {
    updateTask(
        id: "some_id"
        url: "https://mautic.test/cronfig/mautic%3Asegments%3Aupdate?secret_key="
        platform: "Mautic"
        status: "active"
        period: 30
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
