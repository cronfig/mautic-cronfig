<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Http\Adapter\Guzzle6\Client as GuzzleClient;
use MauticPlugin\CronfigBundle\Api\Config;
use MauticPlugin\CronfigBundle\Api\Connection;
use MauticPlugin\CronfigBundle\Api\QueryBuilder;
use MauticPlugin\CronfigBundle\Exception\MissingJwtException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config|MockObject
     */
    private $apiConfig;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    protected function setUp()
    {
        $this->apiConfig    = $this->createMock(Config::class);
        $this->queryBuilder = new QueryBuilder();
        $this->logger       = $this->createMock(LoggerInterface::class);
    }

    public function testQueryWhenJwtCached()
    {
        $client = new Client([
            'handler' => HandlerStack::create(
                new MockHandler([
                    new Response(
                        200,
                        ['X-Foo' => 'Bar'],
                        '{"some":"response"}'
                    ),
                ])
            ),
        ]);

        $connection = new Connection(
            $this->apiConfig,
            new GuzzleClient($client),
            $this->queryBuilder,
            $this->logger
        );

        $this->apiConfig->expects($this->once())
            ->method('getJwt')
            ->willReturn('some_jwt_token');

        $this->assertSame(
            ['some' => 'response'],
            $connection->query('some GQL query')
        );
    }

    public function testQueryWhenJwtMustBeFetchedFirst()
    {
        $client = new Client([
            'handler' => HandlerStack::create(
                new MockHandler([
                    new Response(
                        200,
                        ['X-Foo' => 'Bar'],
                        '{"data": {"signIn": {"token": "some_JWT_token"}}}'
                    ),
                    new Response(
                        200,
                        ['X-Foo' => 'Bar'],
                        '{"some":"response"}'
                    ),
                ])
            ),
        ]);

        $connection = new Connection(
            $this->apiConfig,
            new GuzzleClient($client),
            $this->queryBuilder,
            $this->logger
        );

        $this->apiConfig->expects($this->once())
            ->method('getJwt')
            ->willThrowException(new MissingJwtException());

        $this->apiConfig->expects($this->once())
            ->method('setJwt')
            ->with('some_JWT_token');

        $this->assertSame(
            ['some' => 'response'],
            $connection->query('some GQL query')
        );
    }
}
