<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;
use MauticPlugin\CronfigBundle\Model\CronfigModel;

class AjaxController extends CommonAjaxController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function saveApiKeyAction(Request $request)
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
