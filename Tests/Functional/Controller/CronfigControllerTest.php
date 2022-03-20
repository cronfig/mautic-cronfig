<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;

final class CronfigControllerTest extends MauticMysqlTestCase
{
    protected function setUp(): void
    {
        $this->configParams['site_url'] = 'https://some.url';
        $this->configParams['cronfig'] = [
            'api_key' => 'test.api.key',
            'secret_key' => 'test.secret.key',
        ];

        parent::setUp();
    }

    public function testOutput(): void
    {
        $this->client->request(Request::METHOD_GET, '/s/cronfig');
        Assert::assertTrue($this->client->getResponse()->isSuccessful());
        Assert::assertStringContainsString('email: \'admin@yoursite.com\',', $this->client->getResponse()->getContent());
        Assert::assertStringContainsString('apiKey: \'test.api.key\',', $this->client->getResponse()->getContent());
    }
}
