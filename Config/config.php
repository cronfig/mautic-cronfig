<?php

return [
    'name'        => 'Cronfig',
    'description' => 'Takes care of the cron jobs - makes your Mautic alive.',
    'version'     => '2.0',
    'author'      => 'Cronfig.io',

    'routes' => [
        'main' => [
            'cronfig' => [
                'path'       => '/cronfig',
                'controller' => 'MauticPlugin\CronfigBundle\Controller\CronfigController::indexAction',
            ],
        ],
        'public' => [
            'cronfig_public' => [
                'path'       => '/cronfig/{command}',
                'controller' => 'MauticPlugin\CronfigBundle\Controller\PublicController::triggerAction',
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
];
