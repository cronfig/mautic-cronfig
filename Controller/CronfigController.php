<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\IntegrationsBundle\Exception\PluginNotConfiguredException;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use Symfony\Component\HttpFoundation\Response;

class CronfigController extends CommonController
{
    /**
     * Display the Cronfig Login/Dashboard
     */
    public function indexAction(CronfigModel $model, CoreParametersHelper $coreParametersHelper, UserHelper $userHelper): Response
    {
        $config   = $coreParametersHelper->get('cronfig');
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
                'email'    => $userHelper->getUser()->getEmail(),
                'apiKey'   => $config['api_key'] ?? '',
            ],
            'contentTemplate' => '@Cronfig/index.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#cronfig',
                'mauticContent' => 'cronfig',
                'route'         => $this->generateUrl('cronfig'),
            ],
        ]);
    }
}
