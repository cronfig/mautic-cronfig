<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Model;

use Mautic\CoreBundle\Configurator\Configurator;
use Mautic\CoreBundle\Helper\CacheHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\EncryptionHelper;
use Mautic\CoreBundle\Model\AbstractCommonModel;

/**
 * Class CronfigModel.
 */
class CronfigModel extends AbstractCommonModel
{
    /**
     * Cronfig config params from local.php.
     *
     * @var array
     */
    protected $config;

    /**
     * @var Configurator
     */
    protected $configurator;

    /**
     * @var Constructor
     */
    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        Configurator $configurator,
        CacheHelper $cacheHelper)
    {
        $this->config       = $coreParametersHelper->getParameter('cronfig');
        $this->configurator = $configurator;
        $this->cache        = $cacheHelper;
    }

    /**
     * Return the array of predefined commands.
     *
     * @return array
     */
    public function getCommands()
    {
        return [
            'mautic:segments:update' => [
                'title'       => 'Update segments',
                'description' => 'Updates the contacts in the segments. This command is required for basic Mautic functions.',
            ],
            'mautic:campaigns:rebuild' => [
                'title'       => 'Update campaigns',
                'description' => 'Adds/removes contacts from campaigns. This command is required for basic Mautic functions.',
            ],
            'mautic:campaigns:trigger' => [
                'title'       => 'Trigger campaigns',
                'description' => 'Triggers the campaign events. This command is required for basic Mautic functions.',
            ],
            'mautic:emails:send' => [
                'title'       => 'Process emails',
                'description' => 'Processes the emails in the queue. This command is needed if you configure the emails to be processed in a queue.',
            ],
            'mautic:email:fetch' => [
                'title'       => 'Fetch emails',
                'description' => 'Reads emails from a inbox defined in the Monitored Inbox setting.',
            ],
            'mautic:iplookup:download' => [
                'title'       => 'Update geoIP',
                'description' => 'Downloads/updates the MaxMind GeoIp2 City database. This command is needed only if you use the "MaxMind - GeoIp2 City Download" IP lookup service.',
            ],
            'mautic:social:monitoring' => [
                'title'       => 'Social Monitoring',
                'description' => 'This task must run when you want to add contacts to Mautic through monitoring Twitter for mentions and hashtags.',
            ],
            'mautic:webhooks:process' => [
                'title'       => 'Webhooks',
                'description' => 'If Mautic is configured to send webhooks in batches, use this task to send the payloads.',
            ],
            'mautic:broadcasts:send' => [
                'title'       => 'Send Scheduled Broadcasts',
                'description' => 'Instead of requiring a manual send and wait with the browser window open while ajax batches over the send - this task can now be used.',
            ],
        ];
    }

    /**
     * Return the array of available commands.
     *
     * @return array
     */
    public function getCommandsUrls($commands, $baseUrl)
    {
        $commandsWithUrls = [];
        $secretKey        = '';

        if (isset($this->config['secret_key'])) {
            $secretKey = '?secret_key='.$this->config['secret_key'];
        }

        foreach ($commands as $command => $desc) {
            $commandsWithUrls[] = [
                'url'         => $baseUrl.'cronfig/'.urlencode($command).$secretKey,
                'title'       => $desc['title'],
                'description' => $desc['description'],
            ];
        }

        return $commandsWithUrls;
    }

    /**
     * Save API key to the local.php config file if it's new. Returns the secret key.
     *
     * @param string $apiKey
     *
     * @return string
     *
     * @throws Exception
     */
    public function saveApiKey($apiKey)
    {
        if (!$apiKey) {
            throw new \Exception('cronfig.api.key.empty');
        }

        if (!$this->configurator->isFileWritable()) {
            throw new \Exception('mautic.config.notwritable');
        }

        // Ensure the config has a secret key
        if (empty($this->config['secret_key'])) {
            $secretKey = EncryptionHelper::generateKey();
        } else {
            $secretKey = $this->config['secret_key'];
        }

        // Save the API key and secret key only if it doesn't exist or has changed
        if (empty($this->config['api_key'])
            || empty($this->config['secret_key'])
            || $this->config['api_key'] !== $apiKey
            || $this->config['secret_key'] !== $secretKey) {
            $this->configurator->mergeParameters(
                [
                    'cronfig' => [
                        'api_key'    => $apiKey,
                        'secret_key' => $secretKey,
                    ],
                ]
            );
            $this->configurator->write();

            // We must clear the application cache for the updated values to take effect
            $this->cache->clearContainerFile();
        }

        return $secretKey;
    }
}
