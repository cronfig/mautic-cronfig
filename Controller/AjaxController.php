<?php

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends CommonAjaxController
{
    protected function saveApiKeyAction(Request $request): JsonResponse
    {
        /** @var CronfigModel $model */
        $model     = $this->getModel('cronfig');
        $apiKey    = InputHelper::clean($request->request->get('apiKey'));
        $namespace = InputHelper::clean($request->request->get('namespace', 'cronfig'));
        $response  = ['success' => 0];

        try {
            $response['success']    = 1;
            $response['secret_key'] = $model->saveApiKey($apiKey, $namespace);
        } catch (\Exception $e) {
            $this->addFlash('cronfig.config.not.updated', ['%error%' => $e->getMessage()], 'error');
        }

        return $this->sendJsonResponse($response);
    }
}
