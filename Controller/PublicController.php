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
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

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
        $response = new Response();
        $secretKey = $this->request->query->get('secret_key');
        $config = $this->factory->getParameter('cronfig');
        $logger = $this->get('monolog.logger.mautic');

        if (empty($config['secret_key'])) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $output = 'error: secret key is missing in the configuration';
            $logger->log('error', 'Cronfig: secret key is missing in the configuration');
        } elseif (!$secretKey) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $output = 'error: secret key is missing in the request';
            $logger->log('error', 'Cronfig: secret key is missing in the request');
        } elseif ($config['secret_key'] === $secretKey) {
            $command = explode(' ', urldecode($command));
            $errorCount = $this->request->get('error_count', 0);
            $args = array_merge(['console'], $command);

            if ($errorCount > 2) {
                // Try to force the command if it failed 2 times before
                $args[] = '--force';
            }

            try {
                $input = new ArgvInput($args);
                $output = new BufferedOutput();
                $app = new Application($this->get('kernel'));
                $app->setAutoExit(false);
                $result = $app->run($input, $output);
                $output = $output->fetch();
            } catch (\Exception $exception) {
                $output = $exception->getMessage();
                $response->setStatusCode(500);
                $logger->log('error', 'Cronfig: '.$exception->getMessage());
            }

            // Guess that the output is an error message
            $errorWords = ['--force', 'exception'];

            foreach ($errorWords as $errorWord) {
                if (false !== strpos($output, $errorWord)) {
                    $response->setStatusCode(500);
                }
            }
        } else {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $output = 'error: secret key mismatch';
            $logger->log('error', 'Cronfig: secret key mismatch: '.$config['secret_key'].' != '.$secretKey);
        }

        $response->headers->set('Content-Type', 'text/plain');
        $response->setContent($output);

        return $response;
    }
}
