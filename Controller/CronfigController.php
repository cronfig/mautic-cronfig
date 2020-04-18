<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\CronfigBundle\Model\CronfigModel;

final class CronfigController extends CommonController
{
    /*
     * Display the Cronfig Login/Dashboard
     */
    public function indexAction()
    {
        /** @var CronfigModel $model */
        $model     = $this->getModel('cronfig');
        $baseUrl   = $this->generateUrl('mautic_base_index', [], true);
        $config    = $this->get('mautic.helper.core_parameters')->getParameter('cronfig');
        $email     = $this->get('mautic.helper.user')->getUser()->getEmail();
        $secretKey = empty($config['secret_key']) ? '' : $config['secret_key'];
        $apiKey    = empty($config['api_key']) ? '' : $config['api_key'];
        $commands  = $model->getCommandsWithUrls($baseUrl, $secretKey);

        return $this->delegateView([
            'viewParameters' => [
                'title'    => 'cronfig.title',
                'commands' => $commands,
                'email'    => $email,
                'apiKey'   => $apiKey,
            ],
            'contentTemplate' => 'CronfigBundle:Cronfig:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#cronfig',
                'mauticContent' => 'cronfig',
                'route'         => $this->generateUrl('cronfig'),
            ],
        ]);
    }
}
