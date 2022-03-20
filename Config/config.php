<?php

return [
    'name'        => 'Cronfig',
    'description' => 'Takes care of the cron jobs - makes your Mautic alive.',
    'version'     => '1.0',
    'author'      => 'Cronfig.io',

    'routes' => [
        'main' => [
            'cronfig' => [
                'path'       => '/cronfig',
                'controller' => 'CronfigBundle:Cronfig:index',
            ],
        ],
        'public' => [
            'cronfig_public' => [
                'path'       => '/cronfig/{command}',
                'controller' => 'CronfigBundle:Public:trigger',
                'defaults'   => [
                    'command' => '',
                ],
            ],
        ],
    ],

    'menu' => [
        'admin' => [
            'items' => [
                'cronfig.title' => [
                    'id'        => 'cronfig',
                    'route'     => 'cronfig',
                    'iconClass' => 'fa-clock-o',
                    // 'access'    => 'plugin:cronfig:cronfig:view',
                ],
            ],
        ],
    ],

    'services' => [
        'models' => [
            'mautic.cronfig.model.cronfig' => [
                'class'     => MauticPlugin\CronfigBundle\Model\CronfigModel::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.configurator',
                    'mautic.helper.cache',
                ],
            ],
        ],
    ],
];
