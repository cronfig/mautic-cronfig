<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\Provider;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use MauticPlugin\CronfigBundle\Provider\TaskStatusProvider;
use Doctrine\DBAL\Query\QueryBuilder;

class TaskStatusProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Connection|MockObject
     */
    private $connection;

    /**
     * @var CoreParametersHelper|MockObject
     */
    private $coreParametersHelper;

    /**
     * @var ResultStatement|MockObject
     */
    private $resultStatement;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var TaskStatusProvider
     */
    private $taskStatusProvider;

    protected function setUp()
    {
        $this->connection = $this->createMock(Connection::class);
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->resultStatement = $this->createMock(ResultStatement::class);
        $this->queryBuilder = new QueryBuilder($this->connection);
        $this->taskStatusProvider = new TaskStatusProvider(
            $this->connection,
            $this->coreParametersHelper
        );

        $this->connection->method('createQueryBuilder')->willReturn($this->queryBuilder);
    }

    public function testSegmentsAreActive()
    {
        $this->coreParametersHelper->expects($this->once())
            ->method('getParameter')
            ->with('db_table_prefix')
            ->willReturn('prfx_');

        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT COUNT(*) FROM prfx_lead_lists WHERE (is_published = 1) AND (is_published != "a:0:{}")')
            ->willReturn($this->resultStatement);

        $this->resultStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('33');

        $this->assertTrue($this->taskStatusProvider->segmentsAreActive());

        // Call it once more to test the caching.
        $this->assertTrue($this->taskStatusProvider->segmentsAreActive());
    }

    public function testCampaignsAreActive()
    {
        $this->coreParametersHelper->expects($this->once())
            ->method('getParameter')
            ->with('db_table_prefix')
            ->willReturn('prfx_');

        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT COUNT(*) FROM prfx_campaigns WHERE is_published = 1')
            ->willReturn($this->resultStatement);

        $this->resultStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('33');

        $this->assertTrue($this->taskStatusProvider->campaignsAreActive());

        // Call it once more to test the caching.
        $this->assertTrue($this->taskStatusProvider->campaignsAreActive());
    }
}
