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
        return $this->delegateView(array(
            'viewParameters'  => array(
                'title' => 'cronfig.title'
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
