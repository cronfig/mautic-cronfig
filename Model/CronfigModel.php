<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Model;

use Mautic\CoreBundle\Model\AbstractCommonModel;

/**
 * Class CronfigModel
 */
class CronfigModel extends AbstractCommonModel
{

    /**
     * Return the array of available commands
     *
     * @return array
     */
    public function getCommands()
    {
        return [
            'mautic:segments:update'   => [
                'title'         => 'Update segments',
                'description'   => 'Updates the contacts in the segments. This command is required for basic Mautic functions.'
            ],
            'mautic:campaigns:rebuild' => [
                'title'         => 'Update campaigns',
                'description'   => 'Adds/removes contacts from campaigns. This command is required for basic Mautic functions.'
            ],
            'mautic:campaigns:trigger' => [
                'title'         => 'Trigger campaigns',
                'description'   => 'Triggers the campaign events. This command is required for basic Mautic functions.'
            ],
            'mautic:emails:send'       => [
                'title'         => 'Process emails',
                'description'   => 'Processes the emails in the queue. This command is needed if you configure the emails to be processed in a queue.'
            ],
            'mautic:email:fetch'       => [
                'title'         => 'Fetch emails',
                'description'   => 'Reads emails from a inbox defined in the Monitored Inbox setting.'
            ],
            'mautic:iplookup:download' => [
                'title'         => 'Update geoIP',
                'description'   => 'Downloads/updates the MaxMind GeoIp2 City database. This command is needed only if you use the "MaxMind - GeoIp2 City Download" IP lookup service.'
            ],
            'mautic:social:monitoring' => [
                'title'         => 'Social Monitoring',
                'description'   => 'This task must run when you want to add contacts to Mautic through monitoring Twitter for mentions and hashtags.'
            ],
            'mautic:webhooks:process' => [
                'title'         => 'Webhooks',
                'description'   => 'If Mautic is configured to send webhooks in batches, use this task to send the payloads.'
            ],
            'mautic:broadcasts:send' => [
                'title'         => 'Send Scheduled Broadcasts',
                'description'   => 'Instead of requiring a manual send and wait with the browser window open while ajax batches over the send - this task can now be used.'
            ]
        ];
    }

    /**
     * Return the array of available commands
     *
     * @return array
     */
    public function getCommandsUrls($commands, $baseUrl)
    {
        $commandsWithUrls = [];
        $config = $this->factory->getParameter('cronfig');
        $secretKey = '';

        if (isset($config['secret_key'])) {
            $secretKey = '?secret_key=' . $config['secret_key'];
        }

        foreach ($commands as $command => $desc) {
            $commandsWithUrls[] = [
                'url' => $baseUrl . 'cronfig/' . urlencode($command) . $secretKey,
                'title' => $desc['title'],
                'description' => $desc['description']
            ];    
        }

        return $commandsWithUrls;
    }
}
