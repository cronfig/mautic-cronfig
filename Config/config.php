<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name' => 'Cronfig',
    'description' => 'Takes care of the cron jobs - makes your Mautic alive.',
    'version' => '1.0',
    'author' => 'Cronfig.io',

    'routes' => [
        'main' => [
            'cronfig' => [
                'path' => '/cronfig',
                'controller' => 'CronfigBundle:Cronfig:index',
            ],
        ],
        'public' => [
            'cronfig_public' => [
                'path' => '/cronfig/{command}',
                'controller' => 'CronfigBundle:Public:trigger',
                'defaults' => [
                    'command' => '',
                ],
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'items' => [
                'cronfig.title' => [
                    'id' => 'cronfig',
                    'route' => 'cronfig',
                    'iconClass' => 'fa-clock-o',
                    // 'access'    => 'plugin:cronfig:cronfig:view',
                ],
            ],
        ],
    ],

    'services' => [
        'commands' => [
            'cronfig.command.tasks_status' => [
                'class' => \MauticPlugin\CronfigBundle\Command\TasksStatus::class,
                'tag' => 'console.command',
                'arguments' => ['cronfig.task_service.manager'],
            ],
            'cronfig.command.tasks_manage' => [
                'class' => \MauticPlugin\CronfigBundle\Command\TasksManage::class,
                'tag' => 'console.command',
                'arguments' => ['cronfig.task_service.manager'],
            ],
        ],
        'models' => [
            'cronfig.model.cronfig' => [
                'class' => \MauticPlugin\CronfigBundle\Model\CronfigModel::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.configurator',
                    'mautic.helper.cache',
                ],
            ],
        ],
        'collectors' => [
            'cronfig.collector.mautic_task' => [
                'class' => \MauticPlugin\CronfigBundle\Collector\MauticTaskCollector::class,
            ],
        ],
        'providers' => [
            'cronfig.provider.task_status' => [
                'class' => \MauticPlugin\CronfigBundle\Provider\TaskStatusProvider::class,
                'arguments' => [
                    'database_connection',
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'events' => [
            'cronfig.subscriber.campaign' => [
                'class' => \MauticPlugin\CronfigBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'cronfig.task_service.manager',
                ],
            ],
            'cronfig.subscriber.segment' => [
                'class' => \MauticPlugin\CronfigBundle\EventListener\SegmentSubscriber::class,
                'arguments' => [
                    'cronfig.task_service.manager',
                ],
            ],
        ],
        'taskServices' => [
            'cronfig.task_service.segments_update' => [
                'class' => \MauticPlugin\CronfigBundle\TaskService\SegmentsUpdateTaskService::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'cronfig.provider.task_status',
                ],
            ],
            'cronfig.task_service.campaigns_update' => [
                'class' => \MauticPlugin\CronfigBundle\TaskService\CampaignsUpdateTaskService::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'cronfig.provider.task_status',
                ],
            ],
            'cronfig.task_service.campaigns_trigger' => [
                'class' => \MauticPlugin\CronfigBundle\TaskService\CampaignsTriggerTaskService::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'cronfig.provider.task_status',
                ],
            ],
            'cronfig.task_service.ip_lookup_download' => [
                'class' => \MauticPlugin\CronfigBundle\TaskService\IpLookupDownloadTaskService::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'cronfig.provider.task_status',
                ],
            ],
            'cronfig.task_service.manager' => [
                'class' => \MauticPlugin\CronfigBundle\TaskService\TaskManager::class,
                'arguments' => [
                    'cronfig.collector.mautic_task',
                    'cronfig.api.repository',
                ],
            ],
        ],
        'api' => [
            'cronfig.api.config' => [
                'class' => \MauticPlugin\CronfigBundle\Api\Config::class,
                'arguments' => [
                    'mautic.helper.cache_storage',
                ],
            ],
            'cronfig.api.connection' => [
                'class' => \MauticPlugin\CronfigBundle\Api\Connection::class,
                'arguments' => [
                    'cronfig.api.config',
                    'mautic.guzzle.client',
                    'cronfig.api.query_builder',
                    'monolog.logger.mautic',
                ],
            ],
            'cronfig.api.query_builder' => [
                'class' => \MauticPlugin\CronfigBundle\Api\QueryBuilder::class,
            ],
            'cronfig.api.repository' => [
                'class' => \MauticPlugin\CronfigBundle\Api\Repository::class,
                'arguments' => [
                    'cronfig.api.connection',
                    'cronfig.api.query_builder',
                ],
            ],
        ],
    ],
];
