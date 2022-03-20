<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

final class AjaxControllerTest extends MauticMysqlTestCase
{
    public function testCreatingApiKeyAndSecret(): void
    {
        /** @var CoreParametersHelper $coreParametersHelper */
        $coreParametersHelper = $this->getContainer()->get('mautic.helper.core_parameters');

        $namespace = 'cronfig_test';

        Assert::assertNull($coreParametersHelper->get($namespace));

        $this->client->request(
            Request::METHOD_POST,
            '/s/ajax?action=plugin:cronfig:saveApiKey',
            ['apiKey' => '12345abc', 'namespace' => $namespace]
        );

        // Create the param helper again so it would have the latest changes.
        $coreParametersHelper = new CoreParametersHelper(self::$container);

        Assert::assertCount(2, $coreParametersHelper->get($namespace));
        Assert::assertSame('12345abc', $coreParametersHelper->get($namespace)['api_key']);
        Assert::assertNotEmpty($coreParametersHelper->get($namespace)['secret_key']);

        $this->client->request(
            Request::METHOD_POST,
            '/s/ajax?action=plugin:cronfig:saveApiKey',
            ['apiKey' => '_cleanup_', 'namespace' => $namespace]
        );

        // Create the param helper again so it would have the latest changes.
        $coreParametersHelper = new CoreParametersHelper(self::$container);

        Assert::assertNull($coreParametersHelper->get($namespace));
    }
}
