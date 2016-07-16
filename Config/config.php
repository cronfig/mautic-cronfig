<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Cronfig',
    'description' => 'Takes care of the cron jobs - makes your Mautic alive.',
    'version'     => '1.0',
    'author'      => 'Cronfig.io',

    'routes'      => [
        'main' => [
            'cronfig'         => [
                'path'       => '/cronfig',
                'controller' => 'CronfigBundle:Cronfig:index'
            ]
        ],
        'public' => [
            'cronfig_public' => [
                'path' => '/cronfig/{command}',
                'controller' => 'CronfigBundle:Public:trigger',
                'defaults' => [
                    'command' => ''
                ]
            ]
        ]
    ],

    'menu'     => [
        'admin' => [
            'items'    => [
                'cronfig.title' => [
                    'id'        => 'cronfig',
                    'route'     => 'cronfig',
                    'iconClass' => 'fa-clock-o',
                    // 'access'    => 'plugin:cronfig:cronfig:view',
                ]
            ]
        ]
    ],

    'services' => [
        'models' =>  [
            'mautic.cronfig.model.cronfig' => [
                'class' => 'MauticPlugin\CronfigBundle\Model\CronfigModel',
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.category.model.category',
                    'request_stack',
                    'mautic.helper.ip_lookup',
                    'mautic.helper.core_parameters'
                ]
            ]
        ]
    ],
];
