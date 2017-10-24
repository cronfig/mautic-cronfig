<?php

/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Tests\Model;

use Mautic\CoreBundle\Configurator\Configurator;
use Mautic\CoreBundle\Helper\CacheHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;

class CronfigModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testSaveApiKeyEmpty()
    {
        $model = $this->initModel();
        $model->saveApiKey(null);
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
