<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class AjaxController extends CommonAjaxController
{
    public function saveApiKeyAction(Request $request, CronfigModel $model, TranslatorInterface $translator): JsonResponse
    {
        $apiKey    = InputHelper::clean($request->request->get('apiKey', ''));
        $namespace = InputHelper::clean($request->request->get('namespace', 'cronfig'));
        $response  = ['success' => 0];

        try {
            $response['success']    = 1;
            $response['secret_key'] = $model->saveApiKey($apiKey, $namespace);
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('cronfig.config.not.updated', ['%error%' => $e->getMessage()])
            );
        }

        return $this->sendJsonResponse($response);
    }
}
