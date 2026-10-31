<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Tests\Unit\Model;

use Mautic\CoreBundle\Configurator\Configurator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Symfony\Component\Console\Command\Command;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CronfigModelTest extends TestCase
{
    /**
     * @var CoreParametersHelper&MockObject
     */
    private MockObject $coreParametersHelper;

    /**
     * @var Configurator&MockObject
     */
    private MockObject $configurator;

    /**
     * @var KernelInterface&MockObject
     */
    private MockObject $kernel;

    private CronfigModel $cronfigModel;

    protected function setUp(): void
    {
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->configurator         = $this->createMock(Configurator::class);
        $this->kernel = $this->createMock(KernelInterface::class);
        $container = new ContainerBuilder();
        $container->set('console.command_loader', new FactoryCommandLoader([
            'mautic:segments:update' => static fn () => new Command('mautic:segments:update'),
            'messenger:consume' => static fn () => new Command('messenger:consume'),
        ]));
        $this->kernel->method('getContainer')->willReturn($container);
        $this->kernel->method('getBundles')->willReturn([]);
        $this->cronfigModel         = new CronfigModel(
            $this->coreParametersHelper,
            $this->configurator,
            $this->kernel
        );
    }

    public function testSaveApiKeyEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->cronfigModel->saveApiKey('');
    }

    public function testGetCommandsArrayFormat(): void
    {
        $commands = $this->cronfigModel->getCommands();

        $this->assertCount(2, $commands);

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
        }
    }

    public function testGetCommandsWithUrls(): void
    {
        $this->coreParametersHelper->method('get')->willReturnCallback(
            static fn (string $name) => match ($name) {
                'site_url' => 'https://cronfig.io/',
                'cronfig' => ['secret_key' => 'some-secret'],
                default => null,
            }
        );

        $commands = $this->cronfigModel->getCommandsWithUrls();

        $this->assertCount(2, $commands);

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
            $this->assertTrue(!empty($config['url']));
            $this->assertStringContainsString('?secret_key=some-secret', $config['url']);
            $this->assertStringContainsString('https://cronfig.io/cronfig/', $config['url']);
        }
    }
}
