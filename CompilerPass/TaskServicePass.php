<?php

declare(strict_types=1);

/**
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 *
 * @see        http://cronfig.io
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TaskServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        /** @var Definition $taskServiceProvider */
        $taskServiceProvider = $container->findDefinition('cronfig.provider.task_service');
        $taskServiceDiKeys = array_keys($container->findTaggedServiceIds('cronfig.task.service'));

        foreach ($taskServiceDiKeys as $id) {
            $taskService = $container->findDefinition($id);
            $taskServiceProvider->addMethodCall('addTaskService', [$taskService]);
        }
    }
}
