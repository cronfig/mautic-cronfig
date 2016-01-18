<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Model;

use Mautic\CoreBundle\Model\CommonModel;

/**
 * Class CronfigModel
 */
class CronfigModel extends CommonModel
{

    /**
     * Return the array of available commands
     *
     * @return array
     */
    public function getCommands()
    {
        return array(
            'cache:clear'                   => 'Clears the cache folder',
            'mautic:leadlists:update'       => 'Updates the leads in the lists',
            'mautic:campaigns:update'       => 'Adds/removes leads from campaigns',
            'mautic:campaigns:trigger'      => 'Triggers the campaign events',
            'mautic:email:process'          => 'Processes the emails in the queue',
            'mautic:fetch:email'            => 'Reads emails from an inbox',
            'doctrine:migrations:migrate'   => 'Clears the cache folder',
            'mautic:iplookup:download'      => 'Downloads/updates the MaxMind GeoIp2 City database'
        );
    }

    /**
     * Return the array of available commands
     *
     * @return array
     */
    public function getCommandsUrls($commands, $baseUrl)
    {
        $commandsWithUrls = array();

        foreach ($commands as $command => $description) {
            $commandsWithUrls[] = array(
                'url' => $baseUrl . 'cronfig/' . urlencode($command),
                'description' => $description
            );    
        }

        return $commandsWithUrls;
    }
}
