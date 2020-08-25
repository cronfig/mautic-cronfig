<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\UserBundle\Entity\User;

final class CronfigControllerTest extends MauticMysqlTestCase
{
    public function testOutput(): void
    {
        $this->container->set(
            'mautic.helper.core_parameters',
            new class($this->container) extends CoreParametersHelper {
                public function get($name, $default = null)
                {
                    switch ($name) {
                        case 'site_url':
                            return 'https://some.url';
                        case 'cronfig':
                            return [
                                'api_key' => 'test.api.key',
                                'secret_key' => 'test.secret.key',
                            ];
                        default:
                            return parent::get($name, $default);
                    }
                }
            }
        );

        $this->container->set(
            'mautic.helper.user',
            new class($this->container) extends UserHelper {
                public function __construct()
                {
                }

                public function getUser($nullIfGuest = false)
                {
                    return (new User())->setEmail('john@doe.email');
                }
            }
        );

        $crawler = $this->client->request(
            Request::METHOD_GET,
            '/s/cronfig'
        );

        Assert::assertSame(
            'document.cronfigConfig = {
        platform: \'mautic\',
        tasks: [{"title":"Update segments","description":"Updates the contacts in the segments. This command is required for basic Mautic functions.","url":"https:\/\/some.url\/cronfig\/mautic%3Asegments%3Aupdate?secret_key=test.secret.key"},{"title":"Update campaigns","description":"Adds\/removes contacts from campaigns. This command is required for basic Mautic functions.","url":"https:\/\/some.url\/cronfig\/mautic%3Acampaigns%3Arebuild?secret_key=test.secret.key"},{"title":"Trigger campaigns","description":"Triggers the campaign events. This command is required for basic Mautic functions.","url":"https:\/\/some.url\/cronfig\/mautic%3Acampaigns%3Atrigger?secret_key=test.secret.key"},{"title":"Process emails","description":"Processes the emails in the queue. This command is needed if you configure the emails to be processed in a queue.","url":"https:\/\/some.url\/cronfig\/mautic%3Aemails%3Asend?secret_key=test.secret.key"},{"title":"Fetch emails","description":"Reads emails from a inbox defined in the Monitored Inbox setting.","url":"https:\/\/some.url\/cronfig\/mautic%3Aemail%3Afetch?secret_key=test.secret.key"},{"title":"Update geoIP","description":"Downloads\/updates the MaxMind GeoIp2 City database. This command is needed only if you use the \"MaxMind - GeoIp2 City Download\" IP lookup service.","url":"https:\/\/some.url\/cronfig\/mautic%3Aiplookup%3Adownload?secret_key=test.secret.key"},{"title":"Social Monitoring","description":"This task must run when you want to add contacts to Mautic through monitoring Twitter for mentions and hashtags.","url":"https:\/\/some.url\/cronfig\/mautic%3Asocial%3Amonitoring?secret_key=test.secret.key"},{"title":"Webhooks","description":"If Mautic is configured to send webhooks in batches, use this task to send the payloads.","url":"https:\/\/some.url\/cronfig\/mautic%3Awebhooks%3Aprocess?secret_key=test.secret.key"},{"title":"Send Scheduled Broadcasts","description":"Instead of requiring a manual send and wait with the browser window open while ajax batches over the send - this task can now be used.","url":"https:\/\/some.url\/cronfig\/mautic%3Abroadcasts%3Asend?secret_key=test.secret.key"},{"title":"Send Scheduled Reports","description":"This task is needed for sending scheduled report emails.","url":"https:\/\/some.url\/cronfig\/mautic%3Areports%3Ascheduler?secret_key=test.secret.key"},{"title":"Process background imports","description":"This task is needed for running imports on background so you don\'t have to wait with open browser.","url":"https:\/\/some.url\/cronfig\/mautic%3Aimport?secret_key=test.secret.key"},{"title":"Fetch contacts from Hubspot every 15 minutes","description":"Turn this on to fetch contacts from Hubspot. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Hubspot.","url":"https:\/\/some.url\/cronfig\/mautic%3Aintegration%3Afetchleads+--integration%3DHubspot?secret_key=test.secret.key"},{"title":"Fetch contacts from Salesforce every 15 minutes","description":"Turn this on to fetch contacts from Salesforce. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Salesforce.","url":"https:\/\/some.url\/cronfig\/mautic%3Aintegration%3Afetchleads+--integration%3DSalesforce?secret_key=test.secret.key"},{"title":"Fetch contacts from vTiger every 15 minutes","description":"Turn this on to fetch contacts from vTiger. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to vTiger.","url":"https:\/\/some.url\/cronfig\/mautic%3Aintegration%3Afetchleads+--integration%3DVtiger?secret_key=test.secret.key"},{"title":"Fetch contacts from Sugar CRM every 15 minutes","description":"Turn this on to fetch contacts from Sugar CRM. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Sugar CRM.","url":"https:\/\/some.url\/cronfig\/mautic%3Aintegration%3Afetchleads+--integration%3DSugarcrm?secret_key=test.secret.key"},{"title":"Fetch contacts from Zoho CRM every 15 minutes","description":"Turn this on to fetch contacts from Zoho CRM. It is important to configure the period to 15 minutes for it to work correctly. Use the campaign action to push contacts to Zoho CRM.","url":"https:\/\/some.url\/cronfig\/mautic%3Aintegration%3Afetchleads+--integration%3DZoho?secret_key=test.secret.key"},{"title":"GDPR complience cleanup","description":"Delete data to fulfill GDPR European regulation. This will delete leads that have been inactive for 3 years. WARNING: The deleted data cannot be recovered and it will change Mautic statistics.","url":"https:\/\/some.url\/cronfig\/mautic%3Amaintenance%3Acleanup+--no-interaction+--gdpr?secret_key=test.secret.key"},{"title":"Maintenance: 1 year cleanup","description":"Deletes data for contacts that were not active for 1 year. Currently supported are audit log entries, visitors (anonymous contacts), and visitor page hits. WARNING: The deleted data cannot be recovered and it will change Mautic statistics.","url":"https:\/\/some.url\/cronfig\/mautic%3Amaintenance%3Acleanup+--no-interaction?secret_key=test.secret.key"},{"title":"Maintenance: Deduplicate contacts","description":"It may happen that some duplicate contacts will get into the system somehow. This task will find contacts with the same unique identifiers and merge them.","url":"https:\/\/some.url\/cronfig\/mautic%3Acontacts%3Adeduplicate?secret_key=test.secret.key"},{"title":"Maintenance: Deduplicate contacts","description":"Deletes IP addresses that are not used in any other database table. Those IP adresses usually belonged to contacts that were deleted already.","url":"https:\/\/some.url\/cronfig\/mautic%3Aunusedip%3Adelete?secret_key=test.secret.key"}],
        email: \'john@doe.email\',
        apiKey: \'test.api.key\',
        rememberApiKey: function(apiKey) {
            Mautic.ajaxActionRequest(\'plugin:cronfig:saveApiKey\', \'apiKey=\' + apiKey, function(response) {
                if (typeof response.secret_key !== \'undefined\') {
                    for (var i = 0; i < document.cronfigConfig.tasks.length; i++) {
                        if (document.cronfigConfig.tasks[i].url.indexOf(\'?secret_key=\') === -1) {
                            document.cronfigConfig.tasks[i].url += \'?secret_key=\' + response.secret_key;
                        }
                    }
                }
            }, true);
        }
    }',
            trim($crawler->filter('script#cronfig-config')->first()->text())
        );
    }
}
