<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use MauticPlugin\CronfigBundle\Model\Os\AbstractOs;
use MauticPlugin\CronfigBundle\CronfigBundle;

/**
 * Class PublicController.
 */
class PublicController extends CommonController
{
    /*
     * @param string $command
     */
    public function triggerAction($command)
    {
        $response  = new JsonResponse();
        $secretKey = $this->request->query->get('secret_key');
        $config    = $this->get('mautic.helper.core_parameters')->getParameter('cronfig');
        $logger    = $this->get('monolog.logger.mautic');
        $data      = [];

        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        if (empty($config['secret_key'])) {
            $response->setStatusCode(Codes::HTTP_FORBIDDEN);
            $output = 'error: secret key is missing in the configuration';
            $logger->log('error', 'Cronfig: secret key is missing in the configuration');
        } elseif (!$secretKey) {
            $response->setStatusCode(Codes::HTTP_FORBIDDEN);
            $output = 'error: secret key is missing in the request';
            $logger->log('error', 'Cronfig: secret key is missing in the request');
        } elseif ($config['secret_key'] !== $secretKey) {
            $response->setStatusCode(Codes::HTTP_FORBIDDEN);
            $output = 'error: secret key mismatch';
            $logger->log('error', 'Cronfig: secret key mismatch: '.$config['secret_key'].' != '.$secretKey);
        } else {
            $command    = explode(' ', urldecode($command));
            $errorCount = $this->request->get('error_count', 0);
            $args       = array_merge(['console'], $command);
            $model      = $this->getModel('cronfig');
            $os         = $model->getOs();

            if ($os) {
                $startTime          = microtime(true);
                $initialMemoryUsage = $os->getCurrentMemoryUsage();
            }

            if ($errorCount > 3) {
                // Try to force the command if it failed 2 times before
                $args[] = '--force';
            }

            try {
                $input  = new ArgvInput($args);
                $output = new BufferedOutput();
                $app    = new Application($this->get('kernel'));
                $app->setAutoExit(false);
                $result = $app->run($input, $output);
                $output = $output->fetch();
            } catch (\Exception $exception) {
                $output = $exception->getMessage();
                $response->setStatusCode(Codes::HTTP_INTERNAL_SERVER_ERROR);
                $logger->log('error', 'Cronfig: '.$exception->getMessage());
            }

            // Guess that the output is an error message
            $errorWords = ['--force', 'exception'];

            // Set status 500 if the output contains some keyword
            foreach ($errorWords as $errorWord) {
                if (strpos($output, $errorWord) !== false) {
                    $response->setStatusCode(Codes::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            // Output status 500 if the output is empty
            if (!$output) {
                $response->setStatusCode(Codes::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($os) {
                $executionTime = microtime(true) - $startTime;
                $data['metadata']   = [
                    'platform' => [
                        'name'    => 'Mautic',
                        'version' => $this->get('kernel')->getVersion(),
                    ],
                    'integration' => [
                        'name'    => 'Mautic-Cronfig',
                        'version' => CronfigBundle::VERSION,
                    ],
                    'systemLoad' => [
                        'value' => $os->getLoadPercentage($os->getTimeframeFromExecutionTime($executionTime)),
                        'unit' => '%',
                    ],
                    'ramInitial' => [
                        'value' => $initialMemoryUsage,
                        'unit' => 'b',
                    ],
                    'ramFinal' => [
                        'value' => $os->getCurrentMemoryUsage(),
                        'unit' => 'b',
                    ],
                    'ramPeak' => [
                        'value' => $os->getPeakMemoryUsage(),
                        'unit' => 'b',
                    ],
                    'ramLimit' => [
                        'value' => $os->getMemoryLimit(),
                        'unit' => 'b',
                    ],
                    'runTime' => [
                        'value' => $os->getPercentage($executionTime, $os->getExecutionTimeLimit()),
                        'unit' => '%',
                    ],
                ];
            }
        }

        $data['output'] = $output;

        return $response->setData($data);
    }
}
