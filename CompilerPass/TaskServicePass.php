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

use MauticPlugin\CronfigBundle\TaskService\TaskServiceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class TaskServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        /** @var Definition $mauticTaskCollector */
        $mauticTaskCollector = $containerBuilder->findDefinition('cronfig.collector.mautic_task');

        array_map(function (string $mauticTaskDiKey) use ($containerBuilder, $mauticTaskCollector) {
            $mauticTask = $containerBuilder->findDefinition($mauticTaskDiKey);
            $mauticTaskCollector->addMethodCall('addTask', [$mauticTask]);
        }, $this->findAllKeysByType($containerBuilder, TaskServiceInterface::class));
    }

    private function findAllKeysByType(ContainerBuilder $containerBuilder, string $type): array
    {
        $definitions = [];

        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?: $name;

            // This class has a use statement to unexistent interface which causes an error in the `is_a()` method.
            if ('FOS\\RestBundle\\Serializer\\Normalizer\\FormErrorNormalizer' === $class) {
                continue;
            }

            if (is_a($class, $type, true)) {
                $definitions[] = $name;
            }
        }

        return $definitions;
    }
}
