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

class CronfigModel extends AbstractCommonModel
{
    /**
     * Cronfig config params from local.php.
     *
     * @var array
     */
    private $config;

    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @var CacheHelper
     */
    private $cache;

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
            'mautic:reports:scheduler' => [
                'title'       => 'Send Scheduled Reports',
                'description' => 'This task is needed for sending scheduled report emails.',
            ],
            'mautic:import' => [
                'title'       => 'Process background imports',
                'description' => 'This task is needed for running imports on background so you don\'t have to wait with open browser.',
            ],
            'mautic:integration:fetchleads --integration=Hubspot' => [
                'title'       => 'Fetch contacts from Hubspot every 15 minutes',
                'description' => 'Turn this on to fetch contacts from Hubspot. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Hubspot.',
            ],
            'mautic:integration:fetchleads --integration=Salesforce' => [
                'title'       => 'Fetch contacts from Salesforce every 15 minutes',
                'description' => 'Turn this on to fetch contacts from Salesforce. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Salesforce.',
            ],
            'mautic:integration:fetchleads --integration=Vtiger' => [
                'title'       => 'Fetch contacts from vTiger every 15 minutes',
                'description' => 'Turn this on to fetch contacts from vTiger. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to vTiger.',
            ],
            'mautic:integration:fetchleads --integration=Sugarcrm' => [
                'title'       => 'Fetch contacts from Sugar CRM every 15 minutes',
                'description' => 'Turn this on to fetch contacts from Sugar CRM. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Sugar CRM.',
            ],
            'mautic:integration:fetchleads --integration=Zoho' => [
                'title'       => 'Fetch contacts from Zoho CRM every 15 minutes',
                'description' => 'Turn this on to fetch contacts from Zoho CRM. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Zoho CRM.',
            ],
            'mautic:maintenance:cleanup --no-interaction --gdpr' => [
                'title'       => 'GDPR complience cleanup',
                'description' => 'Delete data to fulfill GDPR European regulation. This will delete leads that have been inactive for 3 years. WARNING: The deleted data cannot be recovered and it will change Mautic statistics.',
            ],
            'mautic:maintenance:cleanup --no-interaction' => [
                'title'       => 'Maintenance: 1 year cleanup',
                'description' => 'Deletes data for contacts that were not active for 1 year. Currently supported are audit log entries, visitors (anonymous contacts), and visitor page hits. WARNING: The deleted data cannot be recovered and it will change Mautic statistics.',
            ],
            'mautic:contacts:deduplicate' => [
                'title'       => 'Maintenance: Deduplicate contacts',
                'description' => 'It may happen that some duplicate contacts will get into the system somehow. This task will find contacts with the same unique identifiers and merge them.',
            ],
            'mautic:unusedip:delete' => [
                'title'       => 'Maintenance: Deduplicate contacts',
                'description' => 'Deletes IP addresses that are not used in any other database table. Those IP adresses usually belonged to contacts that were deleted already.',
            ],
        ];
    }

    /**
     * Return the array of available commands.
     *
     * @return array
     */
    public function getCommandsWithUrls($baseUrl, $secretKey)
    {
        $secretKeyParam = '?secret_key='.$secretKey;

        return array_map(
            function ($command, $commandConfig) use ($baseUrl, $secretKeyParam) {
                $commandConfig['url'] = $baseUrl.'cronfig/'.urlencode($command).$secretKeyParam;

                return $commandConfig;
            },
            array_keys($this->getCommands()),
            $this->getCommands()
        );
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

            // We must clear the application cache for M2 for the updated values to take effect. M3 doesn't need it.
            if (method_exists($this->cache, 'clearContainerFile')) {
                $this->cache->clearContainerFile();
            }
        }

        return $secretKey;
    }
}
