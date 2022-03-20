<?php

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\IntegrationsBundle\Exception\PluginNotConfiguredException;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CronfigController extends CommonController
{
    /**
     * Display the Cronfig Login/Dashboard
     * 
     * @return JsonResponse|Response
     */
    public function indexAction()
    {
        /** @var CronfigModel $model */
        $model = $this->getModel('cronfig');

        /** @var CoreParametersHelper $coreParametersHelper */
        $coreParametersHelper = $this->get('mautic.helper.core_parameters');

        /** @var UserHelper $userHelper */
        $userHelper = $this->get('mautic.helper.user');
        
        $config   = $coreParametersHelper->getParameter('cronfig');
        $email    = $userHelper->getUser()->getEmail();
        $apiKey   = empty($config['api_key']) ? '' : $config['api_key'];
        $error    = null;
        $commands = [];

        try {
            $commands = $model->getCommandsWithUrls();
        } catch (PluginNotConfiguredException $e) {
            $error = $e->getMessage();
        }

        return $this->delegateView([
            'viewParameters' => [
                'title'    => 'cronfig.title',
                'error'    => $error,
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
