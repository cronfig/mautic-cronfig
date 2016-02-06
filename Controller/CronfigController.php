<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

/**
 * Class CronfigController
 */

class CronfigController extends CommonController
{
    /*
     * Display the Cronfig Login/Dashboard
     */
    public function indexAction()
    {
        $model              = $this->factory->getModel('plugin.cronfig.cronfig');
        $commands           = $model->getCommands();
        $baseUrl            = $this->generateUrl('mautic_base_index', array(), true);
        $commandsWithUrls   = $model->getCommandsUrls($commands, $baseUrl);
        $email              = $this->factory->getUser()->getEmail();
        $config             = $this->factory->getParameter('cronfig');
        $apiKey             = '';

        if (isset($config['api_key'])) {
            $apiKey = $config['api_key'];
        }

        return $this->delegateView(array(
            'viewParameters'    => array(
                'title'         => 'cronfig.title',
                'commands'      => $commandsWithUrls,
                'email'         => $email,
                'apiKey'        => $apiKey,
            ),
            'contentTemplate' => 'CronfigBundle:Cronfig:index.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#cronfig',
                'mauticContent' => 'cronfig',
                'route'         => $this->generateUrl('cronfig')
            )
        ));
    }
}
