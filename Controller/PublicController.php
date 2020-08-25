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
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonController
{
    /*
     * @param string $command
     */
    public function triggerAction($command)
    {
        $response  = new Response();
        $secretKey = $this->request->query->get('secret_key');
        $namespace = $this->request->query->get('namespace', 'cronfig');
        $config    = (new CoreParametersHelper($this->container))->getParameter($namespace);
        $logger    = $this->get('monolog.logger.mautic');

        $response->headers->set('Content-Type', 'text/plain');

        if (!$secretKey) {
            $message = 'secret key is missing in the request';
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $logger->log('error', "Cronfig: {$message}");
            $response->setContent("error: {$message}");

            return $response;
        }

        if (empty($config['secret_key'])) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $message = 'secret key is missing in the configuration';
            $logger->log('error', "Cronfig: {$message}");
            $response->setContent("error: {$message}");

            return $response;
        }
        
        if ($config['secret_key'] !== $secretKey) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $logger->log('error', 'Cronfig: secret key mismatch: '.$config['secret_key'].' != '.$secretKey);
            $response->setContent('error: secret key mismatch');

            return $response;
        }

        $command    = explode(' ', urldecode($command));
        $errorCount = $this->request->get('error_count', 0);
        $args       = array_merge(['console'], $command);

        if ($errorCount > 2) {
            // Try to force the command if it failed 2 times before
            $args[] = '--force';
        }

        try {
            $input  = new ArgvInput($args);
            $output = new BufferedOutput();
            $app    = new Application($this->get('kernel'));
            $app->setAutoExit(false);
            $app->run($input, $output);

            $output = "SUCCESS\n\n{$output->fetch()}";
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

        $response->setContent($output);

        return $response;
    }
}
