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
use PHPUnit\Framework\TestCase;

class CronfigModelTest extends TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testSaveApiKeyEmpty()
    {
        $model = $this->initModel();
        $model->saveApiKey(null);
    }

    public function testGetCommandsArrayFormat()
    {
        $model    = $this->initModel();
        $commands = $model->getCommands();

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
        }
    }

    public function testGetCommandsWithUrls()
    {
        $model    = $this->initModel();
        $commands = $model->getCommandsWithUrls('https://cronfig.io/', 'some-secret');

        foreach ($commands as $command => $config) {
            $this->assertTrue(!empty($config['title']));
            $this->assertTrue(!empty($config['description']));
            $this->assertTrue(!empty($config['url']));
            $this->assertContains('?secret_key=some-secret', $config['url']);
            $this->assertContains('https://cronfig.io/cronfig/', $config['url']);
        }
    }

    protected function initModel()
    {
        $coreParametersHelper = $this->getMockBuilder(CoreParametersHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configurator = $this->getMockBuilder(Configurator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheHelper = $this->getMockBuilder(CacheHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new CronfigModel($coreParametersHelper, $configurator, $cacheHelper);
    }
}
