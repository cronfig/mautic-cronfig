<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return array(
    'name'        => 'Cronfig',
    'description' => 'Takes care of the cron jobs - makes your Mautic alive.',
    'version'     => '1.0',
    'author'      => 'Cronfig.io',

    'routes'      => array(
        'main' => array(
            'cronfig'         => array(
                'path'       => '/cronfig',
                'controller' => 'CronfigBundle:Cronfig:index'
            )
        ),
        'public' => array(
            'cronfig_public' => array(
                'path' => '/cronfig/{command}',
                'controller' => 'CronfigBundle:Public:trigger',
                'defaults' => array(
                    'command' => ''
                )
            )
        )
    ),

    'menu'     => array(
        'admin' => array(
            'items'    => array(
                'cronfig.title' => array(
                    'id'        => 'cronfig',
                    'route'     => 'cronfig',
                    'iconClass' => 'fa-share-alt',
                    // 'access'    => 'plugin:cronfig:cronfig:view',
                )
            )
        )
    )
);
