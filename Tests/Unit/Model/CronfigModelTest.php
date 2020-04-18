<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Unit\Model;

use Mautic\CoreBundle\Configurator\Configurator;
use Mautic\CoreBundle\Helper\CacheHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use PHPUnit\Framework\MockObject\MockObject;

class CronfigModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CoreParametersHelper|MockObject
     */
    private $coreParametersHelper;

    /**
     * @var Configurator|MockObject
     */
    private $configurator;

    /**
     * @var CacheHelper|MockObject
     */
    private $cacheHelper;

    /**
     * @var CronfigModel
     */
    private $cronfigModel;

    protected function setUp()
    {
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->configurator         = $this->createMock(Configurator::class);
        $this->cacheHelper          = $this->createMock(CacheHelper::class);

        $this->cronfigModel = new CronfigModel(
            $this->coreParametersHelper,
            $this->configurator,
            $this->cacheHelper
        );
    }

    public function testSaveApiKeyEmpty()
    {
        $this->expectException(\Exception::class);
        $this->cronfigModel->saveApiKey(null);
    }

    public function testGetCommandsArrayFormat()
    {
        $commands = $this->cronfigModel->getCommands();

        foreach ($commands as $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
        }
    }

    public function testGetCommandsWithUrls()
    {
        $commands = $this->cronfigModel->getCommandsWithUrls('https://cronfig.io/', 'some-secret');

        foreach ($commands as $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
            $this->assertTrue(!empty($config['url']));
            $this->assertContains('?secret_key=some-secret', $config['url']);
            $this->assertContains('https://cronfig.io/cronfig/', $config['url']);
        }
    }
}
