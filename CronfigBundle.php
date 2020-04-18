<?php
/**
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 *
 * @see        http://cronfig.io
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use MauticPlugin\CronfigBundle\CompilerPass\TaskServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CronfigBundle extends PluginBundleBase
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TaskServicePass());
    }
}
