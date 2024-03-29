<?php

namespace MauticPlugin\CronfigBundle\Tests\Unit\Model;

use Mautic\CoreBundle\Configurator\Configurator;
use Mautic\CoreBundle\Helper\CacheHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CronfigModelTest extends TestCase
{
    /**
     * @var CoreParametersHelper&MockObject
     */
    private $coreParametersHelper;

    /**
     * @var Configurator&MockObject
     */
    private $configurator;

    /**
     * @var CacheHelper&MockObject
     */
    private $cacheHelper;

    /**
     * @var CronfigModel
     */
    private $cronfigModel;

    protected function setUp(): void
    {
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->configurator         = $this->createMock(Configurator::class);
        $this->cacheHelper          = $this->createMock(CacheHelper::class);
        $this->cronfigModel         = new CronfigModel(
            $this->coreParametersHelper,
            $this->configurator,
            $this->cacheHelper
        );
    }

    public function testSaveApiKeyEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->cronfigModel->saveApiKey(null); /** @phpstan-ignore-line as this is testing a bad input on purpose */
    }

    public function testGetCommandsArrayFormat(): void
    {
        $commands = $this->cronfigModel->getCommands();

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
        }
    }

    public function testGetCommandsWithUrls(): void
    {
        $this->coreParametersHelper->method('getParameter')
            ->withConsecutive(
                ['site_url'],
                ['cronfig']
            )
            ->willReturnOnConsecutiveCalls(
                'https://cronfig.io/',
                ['secret_key' => 'some-secret']
            );

        $commands = $this->cronfigModel->getCommandsWithUrls();

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
            $this->assertTrue(!empty($config['url']));
            $this->assertStringContainsString('?secret_key=some-secret', $config['url']);
            $this->assertStringContainsString('https://cronfig.io/cronfig/', $config['url']);
        }
    }
}
