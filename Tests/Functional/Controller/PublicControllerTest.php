<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\HttpFoundation\Response;

final class PublicControllerTest extends MauticMysqlTestCase
{
    public function testTriggerAction(): void
    {
        /** @var CoreParametersHelper $coreParametersHelper */
        $coreParametersHelper = self::$container->get('mautic.helper.core_parameters');

        $namespace = 'cronfig_test';

        // Firstly create a test credentials.
        $this->client->request(
            Request::METHOD_POST,
            '/s/ajax?action=plugin:cronfig:saveApiKey',
            ['apiKey' => '12345abc', 'namespace' => $namespace]
        );

        // Create the param helper again so it would have the latest changes.
        $coreParametersHelper = new CoreParametersHelper(self::$container);
        $configParams = $coreParametersHelper->get($namespace);

        Assert::assertCount(2, $configParams);

        $secretKey = $configParams['secret_key'];
        $url = "/cronfig/mautic%3Acampaigns%3Atrigger?secret_key={$secretKey}&namespace={$namespace}";

        Assert::assertSame('12345abc', $configParams['api_key']);
        Assert::assertNotEmpty($secretKey);

        // Trigger the campaign trigger action.
        $this->client->request(Request::METHOD_GET, $url);

        $content = $this->client->getResponse()->getContent();

        Assert::assertStringStartsWith('SUCCESS', $content, print_r($configParams, true).$url);
        Assert::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $content);

        // Cleanup.
        $this->client->request(
            Request::METHOD_POST,
            '/s/ajax?action=plugin:cronfig:saveApiKey',
            ['apiKey' => '_cleanup_', 'namespace' => $namespace]
        );

        // Create the param helper again so it would have the latest changes.
        $coreParametersHelper = new CoreParametersHelper(self::$container);

        Assert::assertNull($coreParametersHelper->get($namespace));
    }

    public function testTriggerActionWithMissingSecretInRequest(): void
    {
        $namespace = 'cronfig_test';

        // Trigger the campaign trigger action.
        $this->client->request(
            Request::METHOD_GET,
            "/cronfig/mautic%3Asegments%3Aupdate?namespace={$namespace}"
        );

        Assert::assertSame('error: secret key is missing in the request', $this->client->getResponse()->getContent());
        Assert::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testTriggerActionWithMissingSecretInConfig(): void
    {
        /** @var CoreParametersHelper $coreParametersHelper */
        $coreParametersHelper = self::$container->get('mautic.helper.core_parameters');

        $namespace = 'cronfig_test';

        // Ensure the config is empty.
        $this->client->request(
            Request::METHOD_POST,
            '/s/ajax?action=plugin:cronfig:saveApiKey',
            ['apiKey' => '_cleanup_', 'namespace' => $namespace]
        );

        // Create the param helper again so it would have the latest changes.
        $coreParametersHelper = new CoreParametersHelper(self::$container);

        Assert::assertNull($coreParametersHelper->get($namespace));

        // Trigger the campaign trigger action.
        $this->client->request(
            Request::METHOD_GET,
            "/cronfig/mautic%3Asegments%3Aupdate?secret_key=some_secret&namespace={$namespace}"
        );

        Assert::assertSame('error: secret key is missing in the configuration', $this->client->getResponse()->getContent());
        Assert::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
