<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\Api;

use PHPUnit\Framework\MockObject\MockObject;
use Mautic\CoreBundle\Helper\CacheStorageHelper;
use MauticPlugin\CronfigBundle\Api\Config;
use MauticPlugin\CronfigBundle\Exception\MissingApiKeyException;
use MauticPlugin\CronfigBundle\Exception\MissingJwtException;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CacheStorageHelper|MockObject
     */
    private $cacheStorageHelper;

    /**
     * @var Config
     */
    private $config;

    protected function setUp()
    {
        $this->cacheStorageHelper = $this->createMock(CacheStorageHelper::class);
        $this->config = new Config($this->cacheStorageHelper);
    }

    public function testOnChangeIfPublishedNotChanged()
    {
        $this->assertSame(
            'http://127.0.0.1:3000/graphql', // @todo this must be production URL.
            $this->config->getEndpoint()
        );
    }

    public function testGetApiKeyIfNotInCache()
    {
        $this->expectException(MissingApiKeyException::class);
        $this->config->getApiKey();
    }

    public function testGetApiKeyIfInCache()
    {
        $this->cacheStorageHelper->expects($this->once())
            ->method('get')
            ->with(Config::CACHE_TOKEN_API_KEY)
            ->willReturn('an_api_key');

        $this->assertSame('an_api_key', $this->config->getApiKey());
    }

    public function testSetApiKey()
    {
        $this->cacheStorageHelper->expects($this->once())
            ->method('set')
            ->with(Config::CACHE_TOKEN_API_KEY, 'an_api_key');

        $this->config->setApiKey('an_api_key');
    }

    public function testGetJwtIfNotInCache()
    {
        $this->expectException(MissingJwtException::class);
        $this->config->getJwt();
    }

    public function testGetJwtIfInCache()
    {
        $this->cacheStorageHelper->expects($this->once())
            ->method('get')
            ->with(Config::CACHE_TOKEN_JWT_KEY)
            ->willReturn('a_jwt_token');

        $this->assertSame('a_jwt_token', $this->config->getJwt());
    }

    public function testSetJwt()
    {
        $this->cacheStorageHelper->expects($this->once())
            ->method('set')
            ->with(Config::CACHE_TOKEN_JWT_KEY, 'a_jwt_token');

        $this->config->setJwt('a_jwt_token');
    }
}
